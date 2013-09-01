<p>¿Tenés alguna duda acerca de Guia Gratis? Contactanos.</p>
<p>&nbsp;</p>

<form id="contact" method="post">
  <label>Email
    <input placeholder="tumail@ejemplo.com" name="data[Contact][email]"/>
  </label>
  <label>Consulta
    <textarea placeholder="Quiero saber cómo funciona este sitio..." name="data[Contact][message]"></textarea>
  </label>
  <input type="submit" value="Enviar consulta"/>
</form>
<script type="text/javascript">
 function validateEmail(email)
 {
   if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))
   {
     return true;
   }
   return false;
 }

 $(document).ready(function() {
   $("form#contact").submit(function() {
     if ($("[name=\"data[Contact][email]\"]").val() == "") {
       alert("Por favor, ingresá tu email para que podamos responderte.");
       return false;
     }

     if (!validateEmail($("[name=\"data[Contact][email]\"]").val())) {
       alert("Por favor, ingresá un email válido");
       return false;
     }

     if ($("[name=\"data[Contact][message]\"]").val() == "") {
       alert("Por favor, ingresá un mensaje.");
       return false;
     }
   });
 });
</script>