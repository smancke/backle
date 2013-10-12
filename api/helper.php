<?
/**
 * Creates a callable, which executes the callable
 * and echos the returnd result, converted to json
 */
function wrap($callable){
    $f = function() use($callable) {
        $returnValue = call_user_func_array($callable, func_get_args());
        echo json_encode($returnValue, JSON_UNESCAPED_SLASHES);
    };
    return $f;
}

/**
 * Creates a callable, which 
 * echos the data converted to json
 */
function value($data){
    $f = function() use($data) {
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
    };
    return $f;
}

function urlFor($name, $args=array()) {
    global $app;
    return $app->request->getUrl() . $app->urlFor($name, $args);
}

function userError($message) {
    global $app;
    $app->halt(400, json_encode(['error'=> true, 'message' => $message], JSON_UNESCAPED_SLASHES));
}

function conflictError($message) {
    global $app;
    $app->halt(409, json_encode(['error'=> true, 'message' => $message], JSON_UNESCAPED_SLASHES));
}

function notFoundError($message) {
    global $app;
    $app->halt(404, json_encode(['error'=> true, 'message' => $message], JSON_UNESCAPED_SLASHES));
}