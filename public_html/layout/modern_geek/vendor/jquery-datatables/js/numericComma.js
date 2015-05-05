/**
 * It is not uncommon for non-English speaking countries to use a comma for a
 * decimal place. This sorting plug-in shows how that can be taken account of in
 * sorting by adding the type `numeric-comma` to DataTables. A type detection 
 * plug-in for this sorting method is provided below.
 * 
 * Please note that the 'Formatted numbers' type detection and sorting plug-ins
 * offer greater flexibility that this plug-in and should be used in preference
 * to this method.
 *
 *  @name Commas for decimal place
 *  @summary Sort numbers correctly which use a common as the decimal place.
 *  @deprecated
 *  @author [Allan Jardine](http://sprymedia.co.uk)
 *
 *  @example
 *    $('#example').dataTable( {
 *       columnDefs: [
 *         { type: 'numeric-comma', targets: 0 }
 *       ]
 *    } );
 */

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
	"numeric-comma-pre": function ( a ) {
		var x = (a == "-") ? 0 : a.replace( /,/, "." );
		return parseFloat( x );
	},

	"numeric-comma-asc": function ( a, b ) {
		return ((a < b) ? -1 : ((a > b) ? 1 : 0));
	},

	"numeric-comma-desc": function ( a, b ) {
		return ((a < b) ? 1 : ((a > b) ? -1 : 0));
	}
} );


jQuery.fn.dataTableExt.oSort['formatted-num-asc'] = function(x,y) {
    /* 正規表現で数値と小数点以外は削除する */
    x = x.replace(/[^\d\-\.\/]/g, '');
    y = y.replace(/[^\d\-\.\/]/g, '');
    /* 文字列が分数の場合は除算してソートできるようにする */
    if(x.indexOf('/') >= 0) x = eval(x);
    if(y.indexOf('/') >= 0) y = eval(y);
    return x/1 - y/1;
}
jQuery.fn.dataTableExt.oSort['formatted-num-desc'] = function(x,y) {
    /* 正規表現で数値と小数点以外は削除する */
    x = x.replace(/[^\d\-\.\/]/g, '');
    y = y.replace(/[^\d\-\.\/]/g, '');
    /* 文字列が分数の場合は除算してソートできるようにする */
    if(x.indexOf('/') >= 0) x = eval(x);
    if(y.indexOf('/') >= 0) y = eval(y);
    return y/1 - x/1;
}


/* http://www.datatables.net/examples/plug-ins/range_filtering.html */
/* Custom filtering function which will search data in column four between two values */
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseInt( $('#min').val(), 10 );
        var max = parseInt( $('#max').val(), 10 );
        var price = data[2]; // use data for the price column
 
price = replaceCommas(price);

        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && price <= max ) ||
             ( min <= price   && isNaN( max ) ) ||
             ( min <= price   && price <= max ) )
        {
            return true;
        }
        return false;
    }
);
 

function replaceCommas(amount){ 
	 var amountArray = amount.split(','); 
	 var amountWithoutCommas=''; 
	 for(var index=0;index<amountArray.length;index++){ 
	 	amountWithoutCommas+=amountArray[index]; 
	 } 
	 return parseInt(amountWithoutCommas); 
} 





