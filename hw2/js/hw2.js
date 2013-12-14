	
	/*
	* This is a sort function that will be called every time the lists of sortable numbers has been moved
	* It sorts the array of numbers based on value, with the smallest being first and then appends the new sorted
	* items to the ul. Parameter is the list that needs to be sorted. 
	*/
	function sort(sortArray) {
		var sortableList = $(sortArray);
		var listitems = sortableList.children("li").get();

		listitems.sort(function (a, b) {
			return (parseInt($(a).text()) > parseInt($(b).text())) ? 1 : -1;
		});
		sortableList.append(listitems);
	}
		
	$(document).ready(function(){
	
		$("#chosen li").remove(); // removes un needed placeholder
	
		var request;
		var date = $("#date");
		var loader = $("#loader");
		var chosenBox = $("#chosen");
		var chosenNumsList = $("#chosen ul");
		var fullPicked = false;
		
		/*
		* this is where all of the logic happens when dragging items from one of the boxes to the other, or dragging items within the same box
		*/
		$('#numbers, #chosen ul').sortable({			
			connectWith: ".connectedSortable",
			receive: function( event, ui ) {
				
				var itemParent = $(ui.item).parent();
				var senderItem = $(ui.sender);
				
				var nums = new Array();
				
				nums = $("#chosen li").map(function() {
					return $(this).text();
				}).get();
				
				if(chosenNumsList.children().size() > 6) { // cancel if the picked numbers list is going to be too big, no need for get request
					ui.sender.sortable("cancel");
				} else if(chosenNumsList.children().size() === 0) { //set text to empty as the picked numbers box is empty, no need for get request
					date.text("");
				} else {
					loader.removeClass("hiddenEle");
					if(request != null) {
						request.abort();//aborts the old ajax request, no need to have more than one
					}
					
					request = $.get( 
						"./process.php?numbers=" + nums.toString().split(",").join("-"),  
						function(data) {	
							if(chosenNumsList.children().size() !== 0) {//There is at least 1 number picked 
								date.text(data);
							} else {
								date.text("");
							}
							loader.addClass("hiddenEle");
						},
						"text"
					);	
				}
			},
			update: function(event, ui) {
				sort(this);  
			}
		}).disableSelection();
	});