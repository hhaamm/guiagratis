# Guia Gratis

GuiaGratis es una página que sirve para regalar o pedir cosas de cualquier índole que una persona ya no use o que necesite, y provee una forma de contacto segura y anónima entre estas dos partes. Si ud. tiene algo que quiere regalar y no sabe a quién, o ud. necesita algo y cree que puede encontrarlo gratuitamente, ¡esta es su página!

## ¡No lo tires! Alguien lo necesita

GuiaGratis se rige por un principio máximo: no desperdiciar recursos. La cantidad de basura que se genera en las ciudades hoy en día es alta y muchas veces innecesaria, ya que se tiran objetos en buenas condicioes por el solo hecho de que ocupan demasiado espacio. Si bien hay instituciones como la Iglesia y el Ejército de Salvación que realizan una redistribución de los bienes que a algunas personas no les sirven y a otras si, <b>GuiaGratis</b> propone un nuevo modelo distribuído (en el que también están contempladas estas instituciones como un tipo de usuario especial) mas acorde con los tiempos que corren, en los que la tecnología nos permite una comunicación directa e instantánea entre el donador y el beneficiario. Esto nos permite además ver a la cara al beneficiario, podemos saber (e incluso elegir) a quién realizarle la donación.

## Cuidando al medioambiente

Cualquier tipo de basura industrializada generada por el hombre, independientemente si efectivamente es tóxica o no, es nociva para el medio ambiente, aunque mas no sea por el volúmen que ocupa. Estamos hablando de computadoras, televisores, electrodomésticos, o cualquier otra cosa. Por eso es rehuso y el reciclaje es necesario en nuestra sociedad y <b>GuiaGratis</b> está totalmente a favor del rehuso de cosas que conserven su utilidad, mas allá de que hayan o no quedado anticuadas.

# Instalación

Esta guía de instalación está hecha sobre un sistema Ubuntu 12.04 y el servidor Apache2.

## Prerequisitos

Guia Gratis usa curl, MySQL como base de datos y la extensión de MySQL para PHP.

* sudo apt-get install mysql-server-5.5 mysql-client-5.5 php5 php5-dev php5-curl git-core php5-mysql

## Instalación

1. Crear un virtualhost para Apache y habilitarlo.
2. cd my_install_dir && git clone git@github.com:hhaamm/guiagratis.git .
3. sudo /etc/init.d/apache2 restart
