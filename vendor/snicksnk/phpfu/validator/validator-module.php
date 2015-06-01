<?php

/*
    Validator (error scanner) returns either false (no errors)
    or non-false object, describing errors.

    type Validator error a = a -> (error | false)
*/

// fail : e -> Validator e any
//
// basic validator, will fail with given message on any input
//
function fail($message) {
    return function($_object) use ($message) {
        return $message;
    };
}

// success : () -> Validator any_err any
//
// basic validator, will succeed on any input
//
function success() {
    return function ($_object) {
        return false;
    };
}

// simultaneously : [Validator es a] -> Validator (one-of es) a
//
// validator combiner, will check if all the validators succeeded or return 
// the error message of the first failed one
//
function simultaneously ($predicates) {
    return function ($object) use ($predicates) {
        foreach ($predicates as $pred) {
            $reason = $pred($object);

            if ($reason)
                return $reason;
        }

        return false;
    };
}

// check_all : [Validator es a] -> Validator (array-of es) a
//
// validator combiner, will check if all validators succeeded or return 
// the combined error message of all fails
//
function check_all ($predicates) {
    return function ($object) use ($predicates) {
        $reasons = array();
        $fail = false;

        foreach ($predicates as $pred) {
            $reason = $pred($object);

            $fail = $reason? true : $fail;

            if ($reason)
                $reasons[] = $reason;
        }

        return $fail ? array("fails" => $reasons) : false;
    };
}

// either : [Validator es a] -> Validator (array-of es) a
//
// validator combiner, will check if any validators succeeded or return 
// the combined error message of all fails
//
function either ($predicates) {
    return function ($object) use ($predicates) {
        $reasons = array();

        foreach ($predicates as $pred) {
            $reason = $pred($object);

            if (!$reason)
                return false;

            $reasons[] = $reason; 
        }

        return array("fails" => $reasons);
    };
}

// called : (message, Validator e a) -> Validator message a
//
// validator combiner, will replace an error message for another validator
//
function called($message, $pred) {
    return function ($object) use ($message, $pred) {
        return $pred($object) ? $message : false;
    };
}

// not : Validator e a -> Validator "unknown reason" a
//
// validator combiner, will check if given validator fails
//
// NOTE: it mutes the validator and should be decorated
//
function not ($pred) {
    return function ($object) use ($pred) {
        return $pred($object) ? false : "unknown reason (you shouldn't see this)";
    };
}

function implies($statement, $conclusion) {
    return either(
        not($statement),
        $conclusion);
}

// equals_to : a -> Validator not_equals a
//
// basic validator, checks if an object equals to given one
//
function equals_to($value) {
    $err = "not equals to $value";

    return function ($object) use ($value, $err) {
        return $object == $value ? false : $err;
    };
}

// in_set : set a -> Validator not_in_set a
//
// basic validator, checks if an object belongs to a given set
//
// NOTE: this one will try to implode(', ') the set;
//       you can use in_named_set instead
//
function in_set($set) {
    $shown = implode(', ', $set);
    $err = "not within [$shown]";

    return function ($object) use ($set, $err) {
        return in_array($object, $set) ? false : $err;
    };
}

// in_named_set : name -> set a -> Validator not_in_set a
//
// basic validator, checks if an object belongs to a given set
//
function in_named_set($name, $set) {
    $err = "not within $name";

    return function ($object) use ($set, $err) {
        return in_array($object, $set) ? false : $err;
    };
}

// field : key -> Validator err a -> Validator (field => (key => error)) a
//
// validator combiner, applies given validator to the given field and decorates output
//
function field($key, $pred) {
    return function ($object) use ($key, $pred) {
        $reason = $pred($object[$key]);

        if ($reason) {
            return array("field" => array($key => $reason));
        } else {
            return false;
        };
    };
}

// has_field : key -> Validator (field => (key => not_exists)) a
//
// basic validator, checks if given field exists
//
function has_field($key) {
    $err = array("field" => array("$key" => "not exists"));

    return function ($object) use ($key, $err) {
        return array_key_exists($key, $object) ? false : $err;
    };
}

// the_field_exist : key -> [Validator e a] -> Validator (one-of e) a
//
// constructed validator, checks if given field exists
// and all the predicates hold
//
function the_field_exist($field, $predicates) {
    return simultaneously(
        has_field($field), 
        field($field, simultaneously($predicates))
    );
}

// adapted : func_name -> Validator e a
//
// wrapper around normal predicates
//
// NOTE: should be decorated
//
function adapted($name) {
    return function($object) use ($name) {
        return call_user_func($name, $object)
            ? false
            : "you shouldn't see this"
            ;
    };
}

function matches($regex) {
    $err = "not like $regex";

    return function ($object) use ($regex, $err) {
        return (bool) preg_match($regex, $object) ? false : $err;
    };
}

function show_array($array) {
    $inner = implode(', ', $array);
    return "[$inner]";
}

// preview_validation_result : validation_result -> string
//
// turns validation result into something printable and readable
//
function preview_validation_result($r) {
    switch (gettype($r)) {
        case 'array':
            $result = array();
            $has_string_keys = false;

            foreach ($r as $key => $value) {
                $value         = preview_validation_result($value);
                $key_is_string = gettype($key) == "string";

                $result[]      = $key_is_string? "$key => $value" : $value;

                $has_string_keys = $has_string_keys || $key_is_string;
            }

            $result = implode(", ", $result);

            return $has_string_keys
                ? '{' . $result . '}'
                : '[' . $result . ']';
        
        default:
            return $r ? $r : "succeeded";
    }
}

// print_validation_result : validation_result -> void
//
function print_validation_result($x, $v) {
    echo preview_validation_result($v($x)) . "\n";
}

// suceeded : Validator any_err a -> (a -> boolean)
//
// turns validator into normal predicate
//
function succeeded($v) {
    return function ($x) use ($v) {
        return !$v($x);
    };
}

// $test1 = the_field_exist("a",
//     either(
//         equals_to(5),
//         simultaneously(
//             in_named_set("'things'", 1,2,3, "a"),
            
//             called("full shit",
//                 either(
//                     adapted('is_string'),
//                     fail("you should bee seen this")
//                 )
//             )
//         )
//     )
// );

// print_validation_result(array("a" => 1), $test1);
// print_validation_result(array("a" => 0), $test1);
// print_validation_result(array("a" => 5), $test1);
// print_validation_result(array("a" => array()), $test1);
// print_validation_result(array("a" => "a"), $test1);
// print_validation_result(array("b" => 5), $test1);

function the($view, $predicate) {
    return function($object) use ($view, $predicate) {
        $part   = $view($object);
        $reason = $predicate($part);

        return $reason
            ? array($view => $reason)
            : false
            ;
    };
};

function noLessThan($value) {
    $err = "less than $value";

    return function ($object) use ($value, $err) {
        return $object >= $value? false : $err;
    };
};

$valid_user_reg = check_all(
    the_field_exist("email", 
        matches("/.*@.*/"),

        called("fuck mail.ru",
            not(matches("/.*@mail.ru/"))
        )
    ),
    the_field_exist("pass",
        the("strlen", 
            noLessThan(8))
    )
);

$users = array(
    array(
        "email" => "vasua@rb.ru",
        "pass"  => "12345678"
    ),
    array(
        "email" => "vasua@rb.ru",
        "pass"  => "1234567"
    ),
    array(
        "email" => "vasua@mail.ru",
        "pass"  => "12345678"
    ),
    array(
        "email" => "spam",
        "pass"  => "spam"
    ),
);

foreach ($users as $user) {
    print_validation_result($user, $valid_user_reg);
};
