<?php
require("lib/connect.php");
require("lib/wrapsql.php");
require("lib/util.php");
require("lib/familyTree.php");

?>

<style>

    div{
        margin: 200px;
        position: relative;
        width: 100px;
        height: 120px;
        background-color: red;
    }

    div svg{
        position: absolute;
        right: 0;
        background-color: rgba(0, 255, 0, 0.5);
    }

</style>

<div>
<svg height="120" width="25">
    <path d="M0 60 L25 60" stroke="red" stroke-width="3" ></path>
</svg>

</div>
