<?php

namespace Tests\Unit\Rules;

use Tests\TestCase;
use App\Rules\DocumentRule;
use Tests\Traits\HasDummyCustomer;
use Illuminate\Support\Facades\Validator;

class DocumentRuleTest extends TestCase
{
    use HasDummyCustomer;

    /**
     * Test if can fail if the given document already exists in database.
     *
     * @return void
     */
    public function test_if_can_fail_if_the_given_document_already_exists_in_database(): void
    {
        $cpf = fake()->cpf(false);

        $this->createDummyCustomer(['document_number' => $cpf]);

        $rule = new DocumentRule();

        $validator = Validator::make(['document_number' => $cpf], ['document_number' => $rule]);

        $this->assertFalse($validator->passes());

        $this->assertEquals('The document number is already in use!', $validator->errors()->first('document_number'));
    }

    /**
     * Test if can fail an invalid document.
     *
     * @return void
     */
    public function test_if_can_fail_an_invalid_document(): void
    {
        $cpf = '00100200304';

        $rule = new DocumentRule();

        $validator = Validator::make(['document_number' => $cpf], ['document_number' => $rule]);

        $this->assertFalse($validator->passes());

        $this->assertEquals('The field document number is not a valid CPF.', $validator->errors()->first('document_number'));
    }

    /**
     * Test if can pass a valid cpf.
     *
     * @return void
     */
    public function test_if_can_pass_a_valid_cpf(): void
    {
        $validCpf = fake()->cpf(false);

        $rule = new DocumentRule();

        $validator = Validator::make(['document_number' => $validCpf], ['document_number' => $rule]);

        $this->assertTrue($validator->passes());
    }

    /**
     * Test if can pass a valid cnpj.
     *
     * @return void
     */
    public function test_if_can_pass_a_valid_cnpj(): void
    {
        $validCpf = fake()->cnpj(false);

        $rule = new DocumentRule();

        $validator = Validator::make(['document_number' => $validCpf], ['document_number' => $rule]);

        $this->assertTrue($validator->passes());
    }
}
