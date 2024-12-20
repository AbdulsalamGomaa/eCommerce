<?php 

# abstraction
interface operations {
    #crud
    function create();
    function read();
    function update();
    function delete();
}