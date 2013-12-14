	
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
	
		var request = $.get();
		var date = $("#date");
		var loader = $("#loader");
		var chosenBox = $("#chosen");
		var chosenNumsList = $("#chosen ul");
		var loader = $("#loader");
		
		loader.hide();
		
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
				
				if(chosenNumsList.children().size() > 50) { // cancel if the picked numbers list is going to be too big, no need for get request
					ui.sender.sortable("cancel");
				} else {
					buildGraph();
				}
			},
			update: function(event, ui) {
				sort(this);  
			}
		}).disableSelection();
		
		$("#graphInput button").click(function() {
			buildGraph();
		});
		
		/*
		* called whenever the graph needs to change, if it's from dragging the numbers around or clicking the build graph button for specific dates
		*/
		function buildGraph() {
			loader.show();
			
			var values = $("#graphInput input").map(function(){ //date values
				return $(this).val();
			});
			
			nums = $("#chosen li").map(function() { //numbers in the chosen box
				return $(this).text();
			}).get();
			
			request.abort();
			request = $.get( 
				"./process.php?numbers=" + nums.toString() + "&startDate=" + values[0] + "&endDate=" + values[1],
				function(data) {	
					$("#graph").attr("src", "./google-bar-graph.php?numbers=" + nums.toString() + "&occurrences=" + data);
					loader.hide();
				},
				"text"
			);	
		}
		
	});