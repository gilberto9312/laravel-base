<?php
    function ValidationHeadersContent($request){
        $request->header('Content-Type', 'application/json');
        $contentType = $request->headers->has('Content-Type');
        return $contentType;
    }

    function ValidationHeadersPassword($request){
        $request->header('Password', '');
        $password = $request->headers->has('Password');
        return $password;   
    }

?>