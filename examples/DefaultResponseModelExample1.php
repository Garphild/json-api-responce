<?php

require("../vendor/autoload.php");
$manager = new \Garphild\JsonApiResponse\ApiResponseManager();
$manager->setField("rootName", 1);
// Data: {"status":200,"data":{"rootName":1},"errors":[]}
$manager->setField("rootName.section", 1);
// Data: {"status":200,"data":{"rootName":{"section":1}},"errors":[]}
$manager->setField("rootName.section.subsection", 1);
// Data : {"status":200,"data":{"rootName":{"section":{"subsection":1}}},"errors":[]}
