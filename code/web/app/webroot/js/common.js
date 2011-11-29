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

//Dialog methods
function showLogin() {
    debug('showing login');
    $("#login-dialog").dialog(loginDialogOpts);
    $("#LoginUsername").focus();
}

function showRegister() {
    $("#register-dialog").dialog(registerDialogOpts);
    $("#UserUsername").focus();
}

function showReport(id){
    var delivery_id_report=id;
    $("#report-dialog").dialog(reportDialogOpts);
    $('#ReportDeliveryId').val(delivery_id_report);
}

function showContact() {
    $("#contact-dialog").dialog(contactDialogOpts);
    //$("#UserUsername").focus();
}

$(document).ready(function() {
    loginDialogOpts = {
        title: "Login",
        modal: true,
        autoOpen: true,
        height: 240,
        width: 400,
        resizable: false,
        close: function(){
            $('#LoginUsername').val('');
            $('#login-message').html('');
            $('#LoginPassword').val('');
        },
	buttons: {
	    'Login':function() {
		//$('#login_user').submit();
                data = {
                    'data[User][username]':$('#LoginUsername').val(),
                    'data[User][password]':$('#LoginPassword').val()
                }
                debug(data);
                $.post('/users/login', data, function(data) {
                    if (data.result == 0) {
                        $('#login-message').html(data.message);
                    } else {
                        $(location).attr('href', '/');
                    }
                }, 'json');
	    }
	}
    }

        registerDialogOpts = {
            title: 'Register',
            modal: true,
            autoOpen: true,
            height: 370,
            width: 500,
            resizable: false,
            stack: false,
            close: function(){
                $('.status').html(' ');
                $('#UserUsername').val('');
                $('#UserPassword').val('');
                $('#UserConfirmPassword').val('');
                $('#UserMail').val('');
            },
	    buttons: {
		'Register': function() {
		    $('#register_user').submit();
		}
	    }
        }

        reportDialogOpts = {
            title: 'Report',
            modal: true,
            autoOpen: true,
            height: 530,
            width: 500,
            resizable: false,
            stack: false,
            open: function(){
                $.post('/reports/report_types', function(data) {
                    if (data) {
                    checked='checked'
                    $.each(data, function(key, value) {
                        if (value['ReportTypes']['id'] == InvalidOwnerId && !hasOwner){
                                show=false;
                        }else show=true;
                        if (show){
                            $('#report_type').append('<input type="radio" name="data[Report][type_report]" value="'+value['ReportTypes']['id']+'" '+checked+'>'+value['ReportTypes']['detail']+'<br>');
                            checked = '';
                        }
                    });
                    } else {
                        alert('We have a problem when we tried to get the types report, We are sorry!');
                    }
                }, 'json');
            },
            close: function(){
                 document.getElementById('report_type').innerHTML="";
            },
	    buttons: {
		'Report': function() {
		    $.post("/reports/add", $('#report_delivery').serialize(),function(data){
                        if (data == 1){
                             alert('Your report has been saved and our team is checking it');
                        }else alert('You have reported this delivery');
                    });
                    $(this).dialog('close');
		}
	    }
        }

        contactDialogOpts = {
            title: 'Contact',
            modal: true,
            autoOpen: true,
            height: 430,
            width: 500,
            resizable: false,
            stack: false,
            open: function(){
                $.post('/deliveries/contact_type', function(data) {
                    if (data) {
                    checked=true;
                    $.each(data, function(key, value) {
                        debug(value);
                        $('#contact-type-select').append($("<option></option>").attr("value",value['ContactTypes']['id']).text(value['ContactTypes']['detail']));
                        checked=false;
                    });
                    } else {
                        alert('We have a problem when we tried to get the types contact, We are sorry!');
                    }
                }, 'json');
            },
            close: function(){
                $("#contact-type-select").find('option').remove().end();
                $('#ContactFirstName').val('');
                $('#ContactLastName').val('');
                $('#ContactEmail').val('');
                $('#ContactDetail').val('');
            },
	    buttons: {
		'Send': function() {
		    $.post("/deliveries/contact_us", $('#contact_us').serialize(),function(data){
                        if (data == 1){
                             alert('Thanks you!');
                        }else alert('Ups! We have a problem!');
                    });
                    $(this).dialog('close');
		}
	    }
        }

    $('.help-node').mouseover(function(){
       $(this).fadeTo("slow",1);
    });

    });


    function recommend(did){
        data= {'did':did}
        $.post('/deliveries/recommends',data, function(data) {            
                    if (data) {
                        id='#delivery_'+did;
                        $(id).attr('onClick','javascript:dont_recommend('+did+')');
                        $(id).text('unlike');
                    } else {
                       alert('What do you want to do?');
                    }
                }, 'json');
    }

    function dont_recommend(did){
        data= {'did':did}
        $.post('/deliveries/dont_recommends',data, function(data) {
                    if (data) {
                        id='#delivery_'+did;
                        $(id).attr('onClick','javascript:recommend('+did+')');
                        $(id).text('like');
                    } else {
                       alert('What do you want to do?');
                    }
                }, 'json');
    }

/*
 * Help related functions
 */
var helpMode = {
    enabled:false,
    buttonId:'#help-mode-button',
    enableText: 'Enable help mode',
    disableText: 'Disable help mode',
    className:'.help-node',
    helpDialog: '#help-dialog'
};

var helpDialogOpt = {
    title: 'Help: ',
    modal: true,
    autoOpen: true,
    height: 370,
    width: 500,
    resizable: false,
    stack: false,
    remoteKey: 'fillMeBeforeLoad',
    remoteFullHelpUrl: 'fillMeInOpen',
    open: function() {
	//TODO: fill div 'helpMode.helpDialog' with content
	//debug(helpDialogOpt.remoteKey);
	$.getJSON('/help_tips/get/'+helpDialogOpt.remoteKey, function(data) {
	    $('#help-dialog-description').html(data.HelpTip.description);
	    helpDialogOpt.remoteFullHelpUrl = data.HelpTip.arcticle_url;
	});
    },
    buttons: {
        'Ok': function() {
            $(this).dialog('close');
        },
        'More info': function() {
	    debug(this);
	    window.open(helpDialogOpt.remoteFullHelpUrl);
        }
    }
}

$(document).ready(function() {
    $(helpMode.buttonId).click(toggleHelp);
});

function toggleHelp() {
    helpMode.enabled = !helpMode.enabled;

    if (helpMode.enabled) {
        enableHelpMode();
    } else {
        disableHelpMode();
    }
}

function enableHelpMode() {
    $(helpMode.className).click(clickCallback);
    $(helpMode.buttonId).text(helpMode.disableText);
    //darkenScreen();
    addArrow();
}

function disableHelpMode() {
    $(helpMode.className).unbind('click', clickCallback);
    $(helpMode.buttonId).text(helpMode.enableText);
    //illuminateScreen();
    removeArrow();
}

function clickCallback() {
    helpDialogOpt.remoteKey = $(this).metadata().elementName;
    $(helpMode.helpDialog).dialog(helpDialogOpt);
    debug('Clicking on a help node');
    return false;
}

function darkenScreen(){
  $('#help').css('display', 'block');
  $('#container').fadeTo("slow",0.3);
}

function illuminateScreen(){
    $('#container').fadeTo("slow",1);
    //$('#help_text').css('display', 'none');
    $('#help').css('display', 'none');
}

function addArrow(){
    arrow='<div class="help-arrow"><img src="/img/flecha7.png" width="30"></div>'
    $(helpMode.className).before(arrow);
}

function removeArrow(){
    $('.help-arrow').remove();
}

function changeLanguage(element){
    index=element.selectedIndex;
    value=element.options[index].value;
    $.cookie("language", value, { expires: 7 });
    location.href = location.href;
    return false;
}