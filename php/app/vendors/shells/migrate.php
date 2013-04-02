<?php
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
 *
 */

/*
 * Toma una base de datos MongoDB, copia todos sus datos y la migra a la base
 * de datos MySQL que esté configurada en CakePHP.
 *
 * Usar con precaución! Este shell borra todos los datos de la base de datos.
 */
class MigrateShell extends Shell {
    var $uses = array('User', 'Exchange');  

    function main() {
        if (!Configure::read('Migration.enabled')) {
            echo "This migration cannot be run unless you modify Migration.enabled property in core.php\n";
            return;
        }

        // Deleting database
        $this->Exchange->query('delete from exchange_comments
;');
        $this->Exchange->query('delete from exchange_photos
;');
        $this->Exchange->query('delete from exchanges
;');        
        $this->Exchange->query('delete from messages
;');
        $this->Exchange->query('delete from notification_links;');
        $this->Exchange->query('delete from notifications;');
        $this->Exchange->query('delete from users
;');
        

        $m = new Mongo();
        $db = $m->selectDB('guiagratis');
        $users = $db->users;

        echo "Deleting all uploaded files\n";
        $files = glob(WWW_ROOT.'uploads/'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }

        echo "Deleting all photos from database\n";       

        $usersImported = 0;
        $exchangesImported = 0;
        $photosImported = 0;
        $notificationsImported = 0;
        echo "Importing all users from MongoDB\n";
        foreach($users->find() as $user) {
            // skipping Diego
            // TODO: delete this line!
            if ($user['mail'] == 'd.urriolabeytia@gmail.com')
              continue;

            $this->User->create();
            $user['email'] = $user['mail'];
            $user['modified'] = date('Y-m-d H:i:s', $user['modified']->sec);
            $user['created'] = date('Y-m-d H:i:s', $user['created']->sec);

            // converting username
            $user['username'] = $this->replaceAccents(strtolower(str_replace(' ', '.', $user['username'])));
            echo "Username: ".$user['username']."\n";

            if (isset($user['avatar'])) {
                $img_id = uniqid(null, true);
                $ext = pathinfo($user['avatar']['large']['file_name'], PATHINFO_EXTENSION);

                $avatar_large = $img_id.'_large_square.'.$ext;
                $avatar_medium = $img_id.'_medium_square.'.$ext;
                $avatar_square = $img_id.'_square.'.$ext;

                copy(ROOT.'../../old_images/uploads/'.$user['avatar']['large']['file_name'], WWW_ROOT.'uploads/'.$avatar_large);
                copy(ROOT.'../../old_images/uploads/'.$user['avatar']['medium']['file_name'], WWW_ROOT.'uploads/'.$avatar_medium);
                copy(ROOT.'../../old_images/uploads/'.$user['avatar']['small']['file_name'], WWW_ROOT.'uploads/'.$avatar_square);

                $user['avatar'] = $img_id.'.'.$ext;
            }

            if (!$this->User->save($user)) {
                echo "An error has ocurred when importing user {$user['username']}\n";
                foreach($this->User->validationErrors as $field => $error) {
                    echo $field.': '.$error."\n";
                }
                continue;
            }

            $usersImported++;

            echo "Getting exchanges for this user\n";

            $cursor = $db->exchanges->find(array('user_id' => (string)$user['_id']));
            foreach($cursor as $exchange) {
                $exchange['user_id'] = $this->User->id;
                if (is_object($exchange['created'])) {
                    $exchange['created'] = date('Y-m-d H:i:s', $exchange['created']->sec);
                } else {
                    $exchange['created'] = date('Y-m-d H:i:s', $exchange['created']);
                }
                if (is_object($exchange['modified'])) {
                    $exchange['modified'] = date('Y-m-d H:i:s', $exchange['modified']->sec);
                } else {
                    $exchange['modified'] = date('Y-m-d H:i:s', $exchange['modified']);
                }
                $exchange['tags'] = implode(',', $exchange['tags']);

                $this->Exchange->create();
                if ($this->Exchange->save($exchange)) {
                    $exchange_id = $this->Exchange->id;
                    $exchangesImported++;

                    if (!empty($exchange['photos'])) {
                        foreach ($exchange['photos'] as $photo) {
                            $img_id = $photo['id'];
                            $ext = pathinfo($photo['square']['file_name'], PATHINFO_EXTENSION);

                            $photo_large = $img_id.'.'.strtolower($ext);
                            $photo_small = $img_id.'_small.'.strtolower($ext);
                            $photo_square = $img_id.'_square.'.strtolower($ext);
                            copy(ROOT.'/../old_images/uploads/'.$photo['small']['file_name'], WWW_ROOT.'uploads/'.$photo_small);
  
copy(ROOT.'/../old_images/uploads/'.$photo['square']['file_name'], WWW_ROOT.'uploads/'.$photo_square);

                            // No se estaba guardando la foto original. Copiamos la small.
                            copy(ROOT.'/../old_images/uploads/'.$photo['small']['file_name'], WWW_ROOT.'uploads/'.$photo_large);

                            $photo['file_name'] = $photo_large;
                            $photo['exchange_id'] = $exchange_id;
                            if (isset($photo['default'])) {
                                $photo['is_default'] = $photo['default'];
                            } else {
                                $photo['is_default'] = 0;
                            }
                            
                            $this->Exchange->Photo->create();
                            unset($photo['id']);
                            if ($this->Exchange->Photo->save($photo)) {
                                $photosImported++;
                            } else {
                                var_dump($this->Exchange->Photo->validationErrors);
                                die();
                            }
                        }
                    }
                }
            }
        }

        echo "Users imported: $usersImported\n";
        echo "Exchanges imported: $exchangesImported\n";
        echo "Photos imported: $photosImported\n";
        echo "Notifications imported: $notificationsImported\n";
    }

    private function replaceAccents($string) 
    { 
        return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string); 
    }
}