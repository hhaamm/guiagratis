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
