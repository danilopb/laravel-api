<?php

return [
    'allowed_extensions'=> ['jpg','jpeg', 'png', 'gif'],
    'max_size'=> intval(env('MAX_FILE_SIZE', 500)), // KB
];
