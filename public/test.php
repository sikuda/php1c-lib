<?php
declare(strict_types=1);

namespace Sikuda\Php1c;
require_once __DIR__."/../src/CodeStream.php";

//$stream = new CodeStream();
//$REZULTAT= $stream->makeCode("Перем Результат;", "Результат");
//echo $REZULTAT.'<br>';

//eval("\$REZULTAT = php1C_UndefinedType; \$REZULTAT=123;");
eval("\$REZULTAT = Sikuda\php1C\php1C_UndefinedType;");
echo $REZULTAT;


