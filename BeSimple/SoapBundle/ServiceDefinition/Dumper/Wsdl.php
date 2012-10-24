<?php
/*
 * This file is part of the BeSimpleSoapBundle.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapBundle\ServiceDefinition\Dumper;

use BeSimple\SoapBundle\Converter\TypeRepository;
use BeSimple\SoapBundle\ServiceDefinition\Type;
use Zend\Soap\Wsdl as BaseWsdl;

/**
 * @author Francis Besset <francis.besset@gmail.com>
 */
class Wsdl extends BaseWsdl
{
    private $typeRepository;

    public function __construct(TypeRepository $typeRepository, $name, $uri, $strategy = true)
    {
        $this->typeRepository = $typeRepository;

        parent::__construct($name, $uri, $strategy);
    }

    public function getType($type)
    {
        if ($type instanceof Type) {
            $xmlType = $type->getXmlType();
        } else {
            $xmlType = $this->typeRepository->getXmlTypeMapping($type);
        }

        return $xmlType ?: $this->addComplexType($type);
    }

    public function addBindingOperationHeader(\DOMElement $bindingOperation, array $headers, array $baseBinding)
    {
        foreach ($headers as $header) {
            $inputNode  = $bindingOperation->getElementsByTagName('input')->item(0);

            $headerNode = $this->toDomDocument()->createElement('soap:header');
            $headerNode->setAttribute('part', $header);

            foreach ($baseBinding as $name => $value) {
                $headerNode->setAttribute($name, $value);
            }

            $inputNode->appendChild($headerNode);
        }

        return $bindingOperation;
    }
}