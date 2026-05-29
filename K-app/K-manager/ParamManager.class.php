<?php
/*
 * @license AGPL-3.0
 *
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */
declare(strict_types=1);
class ParamManager
{
    private static ?self $instance=null;
    public string $app_name="";
    public string $app_folder="";
    public string $sql_engine="";
    public string $sql_host="";
    public string $sql_user="";
    public string $sql_pass="";
    public string $sql_database="";
    public string $error_email="";
    public string $public_email="";
    public string $server_name="";
    public string $server_url="";
    public string $email_server="";
    public string $email_smtp_server="";
    public string $email_mail="";
    public string $email_password="";
    public string $email_username="";
    public string $email_port="";
    public string $email_transport="";
//    public string $email_connection=""; /* NOT USED ANYMORE */
    public string $email_encryption="";
    public string $email_verify_peer="";
    public string $email_auth_mode="";
    public string $log_dir_mail="";
    public string $log_directory="";
//    public string $link_for_pdf="";
//    public string $folder_for_request="";
//    public string $email_secretariat_efa="";
//    public string $email_secretariat="";
//    public string $email_comptabilite="";
//    public string $email_maison_hotes="";
//    public string $email_archives="";
//    public string $folder_images="";
//    public string $folder_url_images="";
    public string $admin_email="";
//    public string $site_update="";
    public int $mail_error=0;
    public int $time_making=0;
    public string $site_title="EfA KProject";
    public string $site_root="http://localhost/";
    public string $site_action="http://localhost/action.php";
    public string $site_home_page="http://localhost/index.php";
    public string $site_connection_page="http://localhost/se_connecter.php";
    public string $site_session_name="K-APP_SESSION";
//    public string $image_directory_photo_publication="";
//    public string $image_directory_plano_publication="";
    public string $font="";
    public string $analytics_url="";
    public string $analytics_id="";
    public bool $debug=true;
    public bool $impersonate=false;
    public string $cache_folder="";
    public string $redis_hostname="";
    public string $redis_port="";
    public string $redis_username="";
    public string $redis_password="";
    public string $redis_dbindex="";
    public string $redis_timeout="";
    public string $redis_read_timeout="";
    public string $redis_retry_interval="";
    public string $trusted_proxy="";
    public string $max_nb_attempt_failed="";
    public string $max_attempt_failed_by_minute="";
    private ?HashMap $map=null;

    private function __construct()
    {
        $this->map=new HashMap();
        $this->init();
    }

    public function __destruct()
    {

    }

    public static function getInstance(): ParamManager
    {
        if(is_null(self::$instance))
        {
            self::$instance=new ParamManager();
        }
        return self::$instance;
    }


    private function init() : void
    {
    }

    public function toString() : string
    {
        return $this->sql_database." // ".$this->sql_user." // ".$this->sql_pass;
    }

    public function add(string $key,mixed $value): bool
    {
        return $this->map->put($key,strval($value));
    }

    public function get(string $key): string
    {
        $value=$this->map->get($key);
        if(is_null($value))
        {
            return "";
        }
        return $value;
    }

    public function addOrReplace(string $key,string $value): bool
    {
        return $this->map->putOrReplace($key,$value);
    }

    public function replaceIfExists(string $key,string $value): bool
    {
        return $this->map->replace($key,$value);
    }

    public function getMap() : ?HashMap
    {
        return $this->map;
    }

    /**
     *
     * @return array<string,string>
     */
    public function getAttributes(): array
    {
        $reflection = new ReflectionClass($this);
        $attributes = [];

        foreach ($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            if (is_string($value) || is_int($value))
            {
                if(str_ends_with($property->getName(),"pass")
                        ||str_ends_with($property->getName(),"password"))
                {
                    $attributes[$property->getName()] = "*******";
                }
                else
                {
                    $attributes[$property->getName()] = strval($value);
                }
            }
        }

        return $attributes;
    }
}