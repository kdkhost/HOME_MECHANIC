<?php

namespace Tests;

use Eris\Generator;
use Eris\TestTrait;
use Tests\TestCase;

abstract class PropertyTestCase extends TestCase
{
    use TestTrait;

    /**
     * Configuração padrão para testes de propriedade
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar 100 iterações mínimas para cada propriedade
        $this->minimumEvaluationRatio(0.5);
        $this->shrinkingTimeLimit(60);
    }

    /**
     * Gerar tag padronizada para propriedades
     */
    protected function propertyTag(int $number, string $description): string
    {
        return "Feature: homemechanic-system, Property {$number}: {$description}";
    }
}