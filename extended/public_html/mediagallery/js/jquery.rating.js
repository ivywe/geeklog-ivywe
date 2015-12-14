// +---------------------------------------------------------------------------+
// | Media Gallery Plugin 1.6                                                  |
// +---------------------------------------------------------------------------+
// | $Id:: rating.js 2331 2008-06-03 00:00:30Z mevans0263                     $|
// | Javascript (MooTools v1.11 based) to handle AJAX rating sub-system        |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2005-2008 by the following authors:                         |
// |                                                                           |
// | Mark R. Evans              - mark@gllabs.org                              |
// +---------------------------------------------------------------------------+
// | Based on work by:                                                         |
// | Ryan Masuga, masugadesign.com  - ryan@masugadesign.com                    |
// | Masuga Design                                                             |
// |(http://masugadesign.com/the-lab/scripts/unobtrusive-ajax-star-rating-bar/)|
// | Komodo Media (http://komodomedia.com)                                     |
// | Climax Designs (http://slim.climaxdesigns.com/)                           |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//

$(window).load(function() {
    $('.rater').click(function(e) {
        e.preventDefault();

        var paramString = this.href.replace(/.*\?(.*)/, "$1"); // onclick="sndReq('j=1&q=2&t=127.0.0.1&c=5');
        var paramTokens = paramString.split("&");              // onclick="sndReq('j=1,q=2,t=127.0.0.1,c=5');
        var params = new Array();

        for (j = 0; j < paramTokens.length; j++) {
            var paramName  = paramTokens[j].replace(/(.*)=.*/, "$1"); // j
            var paramValue = paramTokens[j].replace(/.*=(.*)/, "$1"); // 1
            params[paramName] = paramValue;
        }
        $.get('rater_rpc.php',
            { j: params['j'], q: params['q'], t: params['t'], c: params['c'],  s: params['s'] },
            function(data) {
            $('#unit_long' + params['q']).html(data);
        });
        return false;
    });
});
