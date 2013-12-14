	var mainImages;
	var pictureInfo;
	var images;

	$(document).ready(function(){
		mainImages = $("#centeredPicture > img");
		pictureInfo = $("#pictureInfo");
		images = $("img");
		
		/*
		* function that takes in a source (a relative image location) and an indexLocation
		* of where that image name is in the source and returns just the same using grep
		*/
		function getImageName(source, indexLocation) {
			return source = $.grep(source, function(n, i) {
				return i > indexLocation;
			});
		}
		
		images.bind('contextmenu', function(e) { //prevents images from being right clicked on
			return false;
		}); 
		
		images.on('dragstart', function(event) { //prevents dragging of the images
			event.preventDefault(); 
		});
		
		/*
		* when either the next or previous buttons are clicked, this will show the next or previous image depending on what button was clicked.
		*/
		$('#mainWrapper > a').click(function() {
		
			var buttonPressed = $(this).attr("id");
			
			var currentPic = $("#centeredPicture > img.active");
			currentPic.removeClass("active").addClass("inactive");
			
			if(buttonPressed == "nextButton") { //next button is pressed
				if(currentPic.next().length == 0) { //loop over
					mainImages.first().removeClass("inactive").addClass("active");
				} else {
					currentPic.next().removeClass("inactive").addClass("active");
				}
			} 
			
			if(buttonPressed == "previousButton") { //previous button is pressed
				if(currentPic.prev().length == 0) { //loop over
					mainImages.last().removeClass("inactive").addClass("active"); 
				} else {
					currentPic.prev().removeClass("inactive").addClass("active");
				}
			}
			
			var source = getImageName($("#centeredPicture > img.active").attr("src"), 5); //gets the image name 
			pictureInfo.hide().load("./img/alt/" + source.join("") + ".txt").fadeIn(); //loads the new text
		});
		
		/*
		* for when a thumbnail is clicked. Will parse the image name of the thumbnail and then find the corresponding
		* main image and set it's class to the active one and the rest of the main images to not active.
		*/
		$(".thumbNail").click(function(){
			var source = getImageName($(this).attr("src"), 11); //gets the image name
			
			$("#centeredPicture > .active").removeClass("active").addClass("inactive");
			$("#centeredPicture [src='./img/" + source.join("") + "']").removeClass("inactive").addClass("active");
			pictureInfo.hide().load("./img/alt/" + source.join("") + ".txt").fadeIn();
		});
	});