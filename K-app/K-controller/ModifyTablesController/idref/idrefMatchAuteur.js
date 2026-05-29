/**
 * Description of entityEditHandler.js
 *
 * @author David Leconte
 */


/* PPN ID */

var serializerIdRef = {

    stringify: function(data) {
        //console.log(data);
        var message = "";
        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                message += key + "=" + escape(data[key]) + "&";
            }
        }
        return message.substring(0, message.length - 1);
    },

    parse: function(message) {
        var data = {};
        var d = message.split("&");
        var pair, key, value;
        for (var i = 0, len = d.length; i < len; i++) {
            pair = d[i];
            key = pair.substring(0, pair.indexOf("="));
            value = pair.substring(key.length + 1);
            data[key] = unescape(value);
        }
        return data;
    }
};

var idrefLink = "https://www.idref.fr/";
var modalHeight = 600,
    modalWidth = 1000;

var popupClick2 = false;

$(function() {
    // Help to understand better the API : http://documentation.abes.fr/aideidref/helpdeveloper/ch04.html
    let ppnInput = $('input[name^="auteurs_id_ppn_"]');

    if (ppnInput.length) {
        let rowParent = ppnInput.parent().parent().parent();

        rowParent.append(
            '<div class = "col-lg-3"> ' +
            '<a class="btn btn-primary" id="ppn-search" style="color:white" > <i class="fa fa-lg fa-search"></i> Chercher identifiant PPN</button></a>&nbsp;&nbsp;' +
            '<a class="btn btn-primary" target="_blank" id="ppn-link"> <i class="fa fa-lg fa-link"></i> Page IDRef</a>' +
            '</div>'
        );

        $("#ppn-link").css("display", "none");

        $(document.body).append(
            '<div id="iframe-dialog"></div>'
        );

        $('#iframe-dialog').css("padding", "0");

        $("#iframe-dialog").dialog({
            title: 'Chercher identifiant PPN',
            autoOpen: false,
            modal: true,
            height: modalHeight,
            width: modalWidth,
            open: function(event, ui) {
                $('#iframe-dialog').css('overflow', 'hidden');
            }
        });

        $('#ppn-search').click(function() {
            // Sometimes it is necessary to click twice for an unknown reason

            if (!popupClick2) {
                if ($('#iframe-dialog iframe').length) $('#iframe-dialog iframe').remove();

                popupClick2 = true;
                $("#iframe-dialog").append('<iframe src="' + idrefLink + '"></iframe>');

                $('#iframe-dialog iframe').css("width", "100%").css("height", "100%");
                $("#iframe-dialog").dialog("open");

                $('#iframe-dialog iframe').on("load", function() {
                    $('#ppn-search')[0].click();
                });
            } else popupClick2 = false;

            let popup = $('#iframe-dialog iframe')[0].contentWindow;

            let inputLastName = $('input[name^="auteurs_nom_"]');
            let inputFirstName = $('input[name^="auteurs_prenom_"]');
            let inputDescription = $('textarea[name^="auteurs_description_"]');
            let inputPointAcces = $('input[name^="auteurs_point_acces_"]');
            let inputBirth = $('input[name^="auteurs_annee_naissance_"]');
            let inputDeath = $('input[name^="auteurs_annee_deces_"]');

            let completeName = "";

            if (inputLastName.val()) completeName += inputLastName.val();
            if (inputFirstName.val() && inputFirstName.val()) completeName += ", " + inputFirstName.val();

            popup.postMessage(serializerIdRef.stringify({ Init: 'true' }), '*');
            popup.postMessage(serializerIdRef.stringify({
                Index1: "Personal name",
                Index1Value: completeName,
                Index2: "",
                Index2Value: "",
                Filtre1: "/",
                Filtre2: "/",

                // Default values
                z101_a: 'FR',
                z102_a: 'GR',
                z103_a: '19831120',
                z200_a: 'Leconte',
                z200_b: 'David',
                z340_a: 'Developer at École Française d\'Athènes',

                fromApp: "Archimage2",
                AutoClick: "false"
            }), "*");

            if (popupClick2) return;

            window.addEventListener("message", function(e) {
                let data = serializerIdRef.parse(e.data);

                if (data["g"] != null) {
                    ppnInput.val(data['b']);

                    let xmlData = $.parseXML(data['f']);
                    console.log((new XMLSerializer()).serializeToString(xmlData));


                    //                     * Datafields:
                    //                     * 200, a : last name
                    //                     * 200, b : first name
                    //                     * 200, f : birth year (YYYY) and death year if exists (YYYY-YYYY)
                    //                     * 300, a : description
                    //                     * 900, a : point d'accès


                    if (!inputLastName.val()) inputLastName.val($(xmlData).find('datafield[tag="200"] subfield[code="a"]').text());
                    if (!inputFirstName.val()) inputFirstName.val($(xmlData).find('datafield[tag="200"] subfield[code="b"]').text());
                    if (!inputPointAcces.val()) inputPointAcces.val($(xmlData).find('datafield[tag="900"] subfield[code="a"]').text());
                    if (!inputDescription.text()) inputDescription.text($(xmlData).find('datafield[tag="300"] subfield[code="a"]').text());

                    let dates = $(xmlData).find('datafield[tag="200"] subfield[code="f"]');

                    if (dates.text()) 
                    {
                        if (!inputBirth.val() || inputBirth.val()==0) 
                        {
                            let birthYear = dates.text().substring(0, 4);
                            if (!isNaN(birthYear)) inputBirth.val(birthYear);
                        }

                        if ((!inputDeath.val() || inputBirth.val()==0) && dates.text().length > 4) 
                        {
                            let deathYear = dates.text().substring(5, 9);
                            //console.log(deathYear);
                            if (!isNaN(deathYear)) inputDeath.val(deathYear);
                        }
                    }

                    $("#iframe-dialog").dialog('close');

                }
            });

        });

        if (ppnInput.val()) {
            $("#ppn-link").attr('href', idrefLink + ppnInput.val()).css("display", "inline-block");
        }

        ppnInput.change(function() {
            if (ppnInput.val()) $("#ppn-link").attr('href', idrefLink + ppnInput.val()).css("display", "inline-block");

            else $("#ppn-link").css("display", "none");
        });

    }
});
