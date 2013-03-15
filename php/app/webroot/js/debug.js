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

debug_enabled = true;

function debug(message) {
    if (debug_enabled) {
        if (!window.console)
            return;
        console.log(message);
    }
}

function debug_obj(obj, parent) {
       // Go through all the properties of the passed-in object
       for (var i in obj) {
          // if a parent (2nd parameter) was passed in, then use that to
          // build the message. Message includes i (the object's property name)
          // then the object's property value on a new line
          if (parent) { var msg = parent + "." + i + "\n" + obj[i]; } else { var msg = i + "\n" + obj[i]; }
          // Display the message. If the user clicks "OK", then continue. If they
          // click "CANCEL" then quit this level of recursion
          if (debug_enabled)
            if (!confirm(msg)) return;
          // If this property (i) is an object, then recursively process the object
          if (typeof obj[i] == "object") {
             if (parent) { debug_obj(obj[i], parent + "." + i); } else { debug_obj(obj[i], i); }
          }
       }
}

function isdefined( variable)
{
    return (typeof(window[variable]) == "undefined")?  false: true;
}
