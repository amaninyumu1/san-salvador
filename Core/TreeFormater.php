<?php

namespace Root\Core;

use Root\App\Controllers\Controller;
use Root\App\Models\Objects\User;
use Root\App\Models\UserModel;

class TreeFormater extends Controller
{

    /**
     * Undocumente
     *
     * @return string
     */
    public function format()
    {

        $root = $this->allUsers();
        $image = $root->getNodeIcon() == null ? "null" : explode(" AND ", $root->getNodeIcon());
        //var_dump($image[1]); exit();

        $json = " {";
        $json .= "\"name\":\"{$root->getName()}\"";
        $json .= ",\"icon\":\"{$image[1]}\"";
        $json .= ",\"foot\":" . ($root->getFoot() == null ? "null" : $root->getFoot());
        if ($root->hasChilds()) {
            $json .= ",\"childs\": [";
            foreach ($root->getChilds() as $key => $node) {
                $json .= $this->formatChild($node) . (($key != (count($root->getChilds()) - 1)) ? "," : "");
            }
            $json .= "]";
        }
        $json .= "}";
        //header('Content-Type: text/json');
        return $json;
    }

    /**
     * Undocumented function
     *
     * @param User $node
     * @return string
     */
    private function formatChild($node)
    {
        //var_dump($node->getId()); exit();
        $image = $node->getNodeIcon() == null ? "null" : explode(" AND ", $node->getNodeIcon());
        $json = " {";
        $json .= "\"name\":\"{$node->getName()}\"";
        $json .= ",\"icon\":\"{$image[1]}\"";
        $json .= ",\"foot\":" . ($node->getFoot() == null ? "null" : $node->getFoot());
        if ($node->hasChilds()) {
            $json .= ",\"childs\": [";
            foreach ($node->getChilds() as $key => $child) {
                $json .= $this->formatChild($child) . (($key != (count($node->getChilds()) - 1)) ? "," : "");
            }
            $json .= "]";
        }
        $json .= "}";
        return $json;
    }
}
