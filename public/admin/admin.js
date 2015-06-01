angular.module('editableInPlace', ['ui.tinymce'])
.controller("adminEditor", function($scope, $rootScope, editor){
	$scope.blockContent = '';
	$scope.editor = editor;

	$scope.showModal = false;

	$rootScope.$on("startEdit", function(){
		console.log('---')
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
        forced_root_block : '',
        menubar : false,
        toolbar: "undo redo | bold italic | link image | fullscreen | image",
        plugins: [
			"image",
			" fullscreen"
        ]
    };


	$scope.save = function(){
		var html = $scope.blockContent;
		editor.save(html);
		$scope.close();
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

	Editor.prototype.startEdit = function(element, blockId){
		this.html = element.innerHTML;
		this.element = element;
		this.blockId = blockId;
		$rootScope.$broadcast("startEdit");
	}

	Editor.prototype.flush = function(){
		this.currentElement = null;
		this.html = '';
		this.isOn = false;
		this.blockId = false;
	}

	Editor.prototype.save = function(newHTML){
		this.element.innerHTML = newHTML;
		$http({
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            url: 'index.php?module=admin&controller=index&action=save&id='+this.blockId,
	            method: 'post',
	            data:  $.param({html:newHTML}),
	            withParams: true
        	}).success(function(data, status, headers, config) {
		// this callback will be called asynchronously
		// when the response is available
		})
	}
	var editor = new Editor();
	return editor;
})
.directive("isEditable", function(editor){
	return function(scope,element,attrs){

		var prevVal = $(element).css("border");
		$(element).on("mouseover", function(){
			$(this).css("border","1px solid cyan");
		});
		$(element).on("mouseout", function(){
		
			$(this).css("border", prevVal);
		});
		$(element).on('click', function(){

			editor.startEdit(element[0], attrs['isEditable']);
		})
    }
})

angular.module('myapp', ['editableInPlace'])