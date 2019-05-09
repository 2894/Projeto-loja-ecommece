<?php

/**
 * Created by PhpStorm.
 * User: glauber
 * Date: 22/01/18
 * Time: 21:00
 */

namespace Hcode\Model;

use \Macgolddard\DB\Sql;
use Rain\Tpl\Exception;
use Macgolddard\Model;

class Category extends Model
{

    public static function listAll()
    {

        $sql    =   new Sql();

        return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

    }

    public function save(){

        $sql = new Sql();

        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)",
            //Passando os parametros para a procedure na ordem correta
            array(
                ":idcategory"       => $this->getidcategory(),
                ":descategory"      => $this->getdescategory()
            )
        );

        $this->setData($results[0]);

        Category::updateFile();
    }

    public function get($idcategory){

        $sql        = new Sql();

        $results    = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory",[
            ':idcategory'   =>  $idcategory
        ]);

        $this->setData($results[0]);

    }

    public function delete(){

        $sql        =   new Sql();

        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory",[
            ':idcategory'   =>      $this->getidcategory()
        ]);

        Category::updateFile();
    }

    public static function updateFile(){

        $categories = Category::listAll();

        $html = [];

        foreach ($categories as $row) {

            array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."categories-menu.html", implode('', $html));
    }
    
    public function getProducts($related = true){
        
        $sql = new Sql();

        if($related === true){
            
             return $sql->select(
                "SELECT * FROM `tb_products` WHERE idproduct IN(
                    SELECT a.idproduct 
                    FROM `tb_products` a
                    INNER JOIN tb_productscategories AS b ON a.idproduct = b.idproduct
                    WHERE b.idcategory = :idcategory
                )",array(
                    ':idcategory'=>$this->getidcategory()
                ) 
            );  

        }else{
            
            return $sql->select("
                SELECT * FROM `tb_products` WHERE idproduct NOT IN(
                    SELECT a.idproduct 
                    FROM `tb_products` a
                    INNER JOIN tb_productscategories AS b ON a.idproduct = b.idproduct
                    WHERE b.idcategory = :idcategory
                )",
                array(
                    ':idcategory'=>$this->getidcategory()
                ) 
            );
    
        }        
        
    }


}