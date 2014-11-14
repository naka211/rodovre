/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

jQuery.noConflict();

window.onload = function() {

    if( typeof Joomla != 'undefined' ){
        Joomla.submitbutton = submitbutton;
	}
	
};


function trim(str) { return jQuery.trim(str); }

function isUnsignedInteger(s) { return (s.toString().search(/^[0-9]+$/) == 0); }

function monkeyPatchAutocomplete() {

    jQuery.ui.autocomplete.prototype._renderItem = function (ul, item) {

        var keywords = jQuery.trim(this.term).split(' ').join('|');
        var output = item.label.replace(new RegExp("(" + keywords + ")", "gi"), '<span style="font-weight:bold;background-color:yellow;">$1</span>');

        return jQuery("<li>")
            .append(jQuery("<a>").html(output))
            .appendTo(ul);
    };
}


function getjqdd(elem_id,hidden_field,param_task,param_type,ajax_url,validator,autoaddbtn) {
	monkeyPatchAutocomplete();
	jQuery( "#"+elem_id )
		.autocomplete({
			source: function( request, response ) {
				jQuery.getJSON( 
					ajax_url, 
					{option:'com_awocoupon', task:param_task, type:param_type, tmpl:'component', no_html:1,term: request.term}, 
					response 
				);
			},
			autoFocus: true,
			minLength: 2,
			selectFirst: true,
			delay:0,
			select: function( event, ui ) { 
					if(ui.item) { 
						jQuery('#'+elem_id).val(ui.item.label); 
						document.adminForm.elements[hidden_field].value = ui.item.id; 
						jQuery( "#"+elem_id ).trigger("autoadd"); 
						
						jQuery('li.ui-menu-item').remove();  // remove result set
						jQuery(this).select(); // highlight the text
						return false;
					}
			},
			close: function (event, ui) { jQuery( "#"+elem_id ).trigger("validate"); }
		})
		.attr("parameter_id", document.adminForm.elements[hidden_field].value)
		.bind("empty_value", function(event){ document.adminForm.elements[hidden_field].value = ''; })
		.bind("validate", function(event){})
		.bind("autoadd", function(event){})
		.focus(function() { 
				jQuery(this)
					.select()
					.mouseup(function (e) { e.preventDefault(); jQuery(this).unbind("mouseup"); })
				;
		})
	;
	if(validator!=undefined && validator=='check_user') {
		jQuery( "#"+elem_id )
			.bind("validate", function(event){ 
				var html = jQuery.ajax({ 
					url: ajax_url, 
					data: "option=com_awocoupon&task=ajax_user&tmpl=component&no_html=1&term="+jQuery('#'+elem_id).val(),
					//async: false ,
					success: function(rtn){ 
						user_id = rtn*1;
						if(isNaN(user_id) || user_id<=0) {
							document.adminForm.elements[hidden_field].value = ''; 
							jQuery('#'+elem_id).val('');
						}
					}
				}).responseText
			});
	}
	if(autoaddbtn!=undefined) {
		jQuery( "#"+elem_id )
			.bind("autoadd", function(event){ 
				jQuery( "#"+autoaddbtn ).click();
			});
	}
}


//function getjqgrid(elem_id,ajax_url,param_task,param_type,cols,selected_function) {
function getjqgrid(elem_id,ajax_url,options) {

	var my_options = {
		ajax_url:base_url,
		selection_function: '',
		selection_params: {},
		param_task:'ajax_elements_grid',
		param_type:'',
		columns: [],
		label_title: '',
		lang_search: 'Search',
		lang_add: 'Add',
		lang_pq_grid: {
			strLoading: "Loading",
			strAdd: "Add",
			strEdit: "Edit",
			strDelete: "Delete",
			strSearch: "Search",
			strNothingFound: "Nothing found",
			strSelectedmatches:"Selected {0} of {1} match(es)",
			strPrevResult: "Previous Result",
			strNextResult: "Next Result"				
		},
		lang_pq_pager: {
			strPage:"Page {0} of {1}",
			strFirstPage:"First Page",
			strPrevPage:"Previous Page",
			strNextPage:"Next Page",
			strLastPage:"Last Page",
			strRefresh:"Refresh",	
			strRpp:"Records per page:",
			strDisplay:"Displaying {0} to {1} of {2} items."	
		}
		
	};
	jQuery.extend(my_options, options);

	
	{ // define the search
		var pqSearch = {
			txt: "",
			rowIndices: [],
			curIndx: null,
			colIndx: 0,
			sortIndx: null,
			sortDir:null,
			results: null,


			
			allResult: function () {
				$grid1.pqGrid( "setSelection", null );
				var rowIndices = this.rowIndices;
				var curPage = this.curPage;
				var rPP = this.rPP;
				jQuery.each(rowIndices, function(curIndx, val) { 
					indx = rowIndices[curIndx];
					if(curPage>1) indx = indx + (curPage-1)*rPP;
					
					$grid1.pqGrid("setSelection", { rowIndx: indx}); 
				});
			},
			

			search: function () {
				var txt = jQuery("input.pq-search-txt").val().toUpperCase(),
					colIndx = jQuery("select#pq-crud-select-column").val(),
					DM = $grid1.pqGrid("option", "dataModel"),
					sortIndx = DM.sortIndx,
					sortDir = DM.sortDir,
					curPage = DM.curPage,
					rPP = DM.rPP;
				if (txt == this.txt && colIndx == this.colIndx && sortIndx == this.sortIndx && sortDir == this.sortDir && curPage==this.curPage && rPP==this.rPP) {
					return;
				}
				this.rowIndices = [], this.curIndx = null;
				this.sortIndx = sortIndx;
				this.sortDir = sortDir;
				this.curPage = curPage;
				this.rPP = rPP;
				if (colIndx != this.colIndx) {
					//clean the prev column.
					//$grid1.pqGrid("option", "customData", { foundRowIndices: [], txt: "", searchColIndx: colIndx });
					$grid1.pqGrid("option", "customData", null);
					$grid1.pqGrid("refreshColumn", { colIndx: this.colIndx });
					this.colIndx = colIndx;
				}
				//debugger;

				if (txt != null && txt.length > 0) {
					txt = txt.toUpperCase();
					//this.colIndx = $("select#pq-crud-select-column").val();

					var data = DM.data;
					//debugger;
					for (var i = 0; i < data.length; i++) {
						var row = data[i];
						var cell = row[this.colIndx].toUpperCase();
						if (cell.indexOf(txt) != -1) {
							this.rowIndices.push(i);
						}
					}
				}
				
				$grid1.pqGrid("option", "customData", { foundRowIndices: this.rowIndices, txt: txt, searchColIndx: colIndx });
				$grid1.pqGrid("refreshColumn", { colIndx: colIndx });
				this.txt = txt;
			}
		}		
	}
		
		
	{ // build the search elements
		jQuery("#"+elem_id).on("pqgridrender", function (evt, obj) {
			var $toolbar = jQuery("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo(jQuery(".pq-grid-top", this));
			var length = colM.length, selectopt = '';
			for (var i = 0; i < length; i++) { selectopt += '<option value="'+i+'" '+(i==1 ? 'SELECTED' : '')+'>'+colM[i].title+'</option>'; }

			//jQuery("<span>"+my_options['lang_search']+"</span>").appendTo($toolbar);
			jQuery("<input type='text' class='pq-search-txt'/>").appendTo($toolbar);
			jQuery('<select id="pq-crud-select-column">'+selectopt+'</select>').appendTo($toolbar);
			
			
			jQuery("<button type='button'  style='margin:0 5px;padding:0 5px;'>&nbsp;"+my_options['lang_search']+"&nbsp;</button>").appendTo($toolbar).bind("click", function (evt) {
				$grid1.pqGrid("showLoading",null);
				pqSearch.search();
				pqSearch.allResult();
				$grid1.pqGrid("hideLoading",null);

			});
			
			
			jQuery("<span class='pq-separator' style='display:inline;'></span>").appendTo($toolbar);
			
			  
			jQuery('<button type="button"  style="margin:0 5px;padding:3px 10px;background-color:#FF4000;color:#ffffff;font-weight:bold;">&nbsp;'+my_options['lang_add']+'&nbsp;</button>').appendTo($toolbar).bind("click", function (evt) {
				var selectionArray= $grid1.pqGrid( "selection", { type:'row', method:'getSelection' } );
				var DM = $grid1.pqGrid("option", "dataModel");
				for(i=0; i<selectionArray.length; i++) {
					indx = selectionArray[i].rowIndx;
					if(DM.curPage>1) indx = indx - (DM.curPage-1)*DM.rPP;

					id = DM.data[indx][0];
					label = DM.data[indx][1];
					
					if(my_options['selection_function']!='') {
						window[my_options['selection_function']](id,label,my_options['selection_params']);
					}
					//id = $grid1.pqGrid( "getCell", {rowIndx: selectionArray[i].rowIndx,colIndx: 0} ).text();
					//label = $grid1.pqGrid( "getCell", {rowIndx: selectionArray[i].rowIndx,colIndx: 1} ).text();
					//alert(id+' '+label);
				}
			});

			
			

		});		
	}
			
	
	{ // define the grid	
		
		var colM = [];
		for (key in my_options['columns']) { colM.push( {title: my_options['columns'][key], width:100 } ); }
		
		var dataModel = {
			location: "remote",
			sorting: "remote",
			paging: "remote",
			dataType: "JSON",
			method: "GET",
			curPage: 1,
			rPP: 100,
			sortIndx: 1,
			sortDir: "up",
			rPPOptions: [5, 10, 20, 50, 100, 500, 1000, 5000],
			getUrl: function () {
				var sortDir = (this.sortDir == "up") ? "asc" : "desc";
				var sort = [];
				for (key in my_options['columns']) { sort.push( key ); }
				return { 
					url: ajax_url,
					data: "option=com_awocoupon&task="+my_options['param_task']+"&type="+my_options['param_type']+"&tmpl=component&no_html=1&cur_page=" + this.curPage + "&records_per_page=" + this.rPP + "&sortBy=" + sort[this.sortIndx] + "&dir=" + sortDir 
				};
			},
			getData: function (dataJSON) {
				return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: dataJSON.data };                
			}
		}
	}

	
	{ // load the grid
		var $grid1 = jQuery("#"+elem_id).pqGrid({
			width: "100%",
			height: 350,
			dataModel: dataModel,
			colModel: colM,
			title:my_options['label_title'],
			resizable: true,            
			columnBorders: true,
			freezeCols: 1
			
			,selectionModel:{type:'row',mode: 'range'}
			,editable:false
			,editModel: {clicksToEdit:2}
			,hoverMode:"row"
			,numberCell:false
			,cellClick: function( event, ui ) { pqgrid_index = ui.rowIndx; }
			,cellDblClick: function( event, ui,ui2 ) { 
				indx = pqgrid_index;
				if(ui.dataModel.curPage>1) indx = indx - (ui.dataModel.curPage-1)*ui.dataModel.rPP;
					
				id = ui.dataModel['data'][indx][0];
				label = ui.dataModel['data'][indx][1];
					
				if(my_options['selection_function']!='') {
					window[my_options['selection_function']](id,label,my_options['selection_params']);
				}
				
			}
			
		});
		
			
		//fix width
		jQuery.each(colM, function(curIndx, val) { 
			if(curIndx==0) colM[curIndx].width = 50;
			else colM[curIndx].width = parseInt(($grid1.width()-50)/(colM.length-1));

		});
		$grid1.pqGrid( "option", "colModel", colM );		
	
		// set language	

		$grid1.pqGrid("option", my_options['lang_pq_grid']);
		$grid1.find(".pq-pager").pqPager("option", my_options['lang_pq_pager']);		

	}
		
		

		
}

