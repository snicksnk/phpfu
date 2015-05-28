angular.module('editableInPlace', ['ui.tinymce'])
.controller("adminEditor", function($scope, $rootScope, editor){
	$scope.blockContent = '';
	$scope.editor = editor;
	console.log(editor);

	$scope.showModal = false;
	$rootScope.$on("startEdit", function(){
		$scope.blockContent = editor.html;
		$scope.showModal = true;
		$scope.$apply();
	});
 
	$scope.tinymceOptions = {
	    handle_event_callback: function (e) {
	    // put logic here for keypress
	    },
	    force_br_newlines : true,
        force_p_newlines : false,
        forced_root_block : ''
        
    };


	$scope.save = function(){
		var html = $scope.blockContent;
		editor.save(html);
	};

	$scope.close = function(){
		$scope.showModal = false;
		editor.flush();
	};
})
.service("editor", function($http, $rootScope){
	var Editor = function(){
		this.flush();
	}

	Editor.prototype.startEdit = function(element){
		this.html = element.innerHTML;
		this.element = element;
		console.log(this);
		$rootScope.$broadcast("startEdit");
	}

	Editor.prototype.flush = function(){
		this.currentElement = null;
		this.html = '';
		this.isOn = false;
	}

	Editor.prototype.save = function(newHTML){
		this.element.innerHTML = newHTML;
	}
	var editor = new Editor();
	return editor;
})
.directive("isEditable", function(editor){
	return function(scope,element,attrs){
		$(element).click(function(){
			editor.startEdit(element[0]);
		})
    }
})

angular.module('myapp', ['editableInPlace'])