/*
 * Guia Gratis, sistema para intercambio de regalos.
 * Copyright (C) 2011  Hugo Alberto Massaroli
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
//jQuery.noConflict();

$(document).ready(function() {
    tagsTextBoxList = new $.TextboxList('#form_tags_input', {unique: true, plugins: {
            autocomplete: {
                minLenght: 3,
                queryRemote: true,
                remote: {url: '/tags/get'}
            }},
        max: 10
    });

    //TODO: see if this is working in all browsers.
    if (typeof(tagsAdded) == 'undefined')
        return;
    //debug(tagsAdded);
    //Adding already linked tags.
    for (i=0; i<tagsAdded.length; i++) {
        tagsTextBoxList.add(tagsAdded[i][1],tagsAdded[i][2]);
    }

    $('#ExchangeSaveOwner').click(function () {
        var save_owner = $('#ExchangeSaveOwner').attr('checked');
        if (save_owner) {
            $('#owner-contract').dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        $(this).dialog('close');
                    },
                    Cancel: function() {
                        $('#ExchangeSaveOwner').attr('checked', false);
                        $(this).dialog('close');
                    }
                }
            });
        } else {
            $('#not-owner-contract').dialog({
                modal:true,
                buttons: {
                    Ok: function() {
                        $(this).dialog('close');
                    },
                    Cancel: function() {
                        $('#ExchangeSaveOwner').attr('checked', true);
                        $(this).dialog('close');
                    }
                }
            });
        }
    });
});

var extraPhonesNumber = 0;

function addAnotherPhone(value, id) {
    debug("Adding another phone");
    extraPhonesNumber++;
    if (value === undefined) {
        value = '';
        id_input = '';
    } else {
        id_input = "<input id='extraPhoneId"+extraPhonesNumber+"' type='hidden' name='data[Phone]["+extraPhonesNumber+"][id]' value='"+id+"'/>";
    }
    $("#extra-phones").append("<div class='extra-phone input text' id='extraPhone"+extraPhonesNumber+"'><label for='"+extraPhonesNumber+"'>Phone"+extraPhonesNumber+" <a href='javascript: removePhone("+extraPhonesNumber+")'>(Remove)</a></label> <input type='text' name='data[Phone]["+extraPhonesNumber+"][detail]' value='"+value+"'/></div>");
    $("#extra-phones").append(id_input);
    debug("Extra phones number: "+extraPhonesNumber);
}

function removePhone(phoneNumber) {
    $("#extraPhone"+phoneNumber).detach();
    $("#extraPhoneId"+phoneNumber).detach();
}


function changePreview(name,hex){
    if (name=='sticker_bg_color'){
        $('#sticker_bg_color').css('background-color', '#'+hex);
    }else if (name =='sticker_font_color'){
            $('#sticker_bg_color').css('color', '#'+hex);
    }else $('#sticker_title_color').css('color', '#'+hex);
}