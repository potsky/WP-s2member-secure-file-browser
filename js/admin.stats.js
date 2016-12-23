;
(function ( $ ) {
	$.extend( $.tablesorter.themes.bootstrap , {
		table      : "table table-bordered table-hover table-condensed" ,
		header     : "bootstrap-header" , // give the header a gradient background
		footerRow  : "" ,
		footerCells: "" ,
		icons      : "" , // add "icon-white" to make them white; this icon class is added to the <i> in the header
		sortNone   : "bootstrap-icon-unsorted" ,
		sortAsc    : "icon-chevron-up" ,
		sortDesc   : "icon-chevron-down" ,
		active     : "" , // applied when column is sorted
		hover      : "" , // use custom css here - bootstrap class may not override it
		filterRow  : "" , // filter row class
		even       : "" , // odd row zebra striping
		odd        : ""  // even row zebra striping
	} );

	$.tablesorter.addParser( {
		id    : 'data' ,
		is    : function ( s ) {
			return false;
		} ,
		format: function ( s , table , cell , cellIndex ) {
			var $cell = $( cell );
			if ( cellIndex === 0 ) {
				return $cell.attr( 'data-t' ) || s;
			}
			return s;
		} ,
		type  : 'numeric'
	} );

	$( "table.sort" ).tablesorter( {
		theme         : "bootstrap" , // this will
		widthFixed    : true ,
		headerTemplate: "{content} {icon}" , // new in v2.7. Needed to add the bootstrap icon!
		widgets       : [ "uitheme", "filter", "zebra"] ,
		headers       : { 0: { sorter: 'data' }} ,
		widgetOptions : {
			zebra       : ["even", "odd"] ,
			filter_reset: ".reset" ,
			uitheme     : "bootstrap"
		}
	} ).tablesorterPager( {
			container : $( ".pager" ) ,
			cssGoto   : ".pagenum" ,
			removeRows: false ,
			// possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
			output    : "{startRow} - {endRow} / {filteredRows} ({totalRows})"
		} );

	$( "table.sortn" ).tablesorter( {
		theme         : "bootstrap" , // this will
		widthFixed    : true ,
		headerTemplate: "{content} {icon}" , // new in v2.7. Needed to add the bootstrap icon!
		widgets       : [ "uitheme", "filter", "zebra"] ,
		widgetOptions : {
			zebra       : ["even", "odd"] ,
			filter_reset: ".reset" ,
			uitheme     : "bootstrap"
		}
	} ).tablesorterPager( {
			container : $( ".pager" ) ,
			cssGoto   : ".pagenum" ,
			removeRows: false ,
			// possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
			output    : "{startRow} - {endRow} / {filteredRows} ({totalRows})"
		} );
}( jQuery ));
