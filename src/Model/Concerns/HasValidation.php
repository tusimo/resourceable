<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

use Hyperf\Validation\Validator;
use Tusimo\Resource\Model\Register;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

trait HasValidation
{
    /**
     * Creating Method.
     */
    public static $METHOD_CREATING = 'create';

    /**
     * Updating Method.
     */
    public static $METHOD_UPDATING = 'update';

    /**
     * The rules for validation for both create and update.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * The rules for validation for resource creating.
     *
     * @var array
     */
    protected $creatingRules = [];

    /**
     * The rules for validation for resource updating.
     *
     * @var array
     */
    protected $updatingRules = [];

    /**
     * Flag for skip validation.
     *
     * @var bool
     */
    protected $skipValidation = false;

    /**
     * Flag skip creating validation.
     *
     * @var bool
     */
    protected $skipCreatingValidation = false;

    /**
     * Flag skip for updating validation.
     *
     * @var bool
     */
    protected $skipUpdatingValidation = false;

    /**
     * Validator.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Skip Validation.
     *
     * @param bool $skip
     */
    public function skipValidation($skip = true): self
    {
        $this->skipValidation = $skip;
        return $this;
    }

    public function skipCreatingValidation($skip = true): self
    {
        $this->skipCreatingValidation = $skip;
        return $this;
    }

    public function skipUpdatingValidation($skip = true): self
    {
        $this->skipUpdatingValidation = $skip;
        return $this;
    }

    public function validate(string $method): static
    {
        if ($this->shouldSkipValidate($method)) {
            return $this;
        }
        if (! $this->getValidatoryFactory()) {
            return $this;
        }
        if (empty($this->getValidationAttributes($method))) {
            return $this;
        }
        if (is_null($this->getValidator())) {
            return $this;
        }
        $this->getValidator()
            ->setData($this->getValidationAttributes($method))
            ->setRules($this->getValidationRules($method))
            ->validate();
        $this->validateCallable($method);
        return $this;
    }

    public function getMethodValidationRules(string $method): array
    {
        switch ($method) {
            case static::$METHOD_UPDATING:
                return $this->getUpdatingRules();
            case static::$METHOD_CREATING:
                return $this->getCreatingRules();
        }
        return $this->rules;
    }

    public function getValidationRules(string $method): array
    {
        $rules = array_merge($this->rules, $this->getMethodValidationRules($method));
        if ($method == static::$METHOD_UPDATING) {
            // auto remove required from rules
            $newRules = [];
            foreach ($rules as $key => $rule) {
                if (is_string($rule)) {
                    $ruleArray = explode('|', $rule);
                    $ruleArray = array_filter($ruleArray, function ($item) {
                        return $item !== 'required';
                    });
                    $newRules[$key] = implode('|', $ruleArray);
                } else {
                    $newRules[$key] = $rule;
                }
            }
            return $newRules;
        }
        return $rules;
    }

    public function getMethodValidationAttributes(string $method): array
    {
        switch ($method) {
            case static::$METHOD_UPDATING:
                return $this->getUpdatingValidationAttributes();
            case static::$METHOD_CREATING:
                return $this->getCreatingValidationAttributes();
        }
        return $this->getAttributes();
    }

    public function shouldMethodValidate(string $method): bool
    {
        switch ($method) {
            case static::$METHOD_UPDATING:
                return $this->skipUpdatingValidation;
            case static::$METHOD_CREATING:
                return $this->skipCreatingValidation;
        }
        return $this->skipValidation;
    }

    public function shouldSkipValidate(string $method): bool
    {
        return $this->skipValidation || $this->shouldMethodValidate($method);
    }

    public function getValidationAttributes(string $method): array
    {
        return array_merge(
            $this->getAttributes(),
            $this->getMethodValidationAttributes($method)
        );
    }

    /**
     * Get Validatory Factory.
     */
    public function getValidatoryFactory(): ?ValidatorFactoryInterface
    {
        return Register::getValidatorFactory();
    }

    /**
     * Get validator.
     *
     * @return ?Validator
     */
    public function getValidator()
    {
        if (is_null($this->getValidatoryFactory())) {
            return null;
        }
        if (is_null($this->validator)) {
            $this->validator = $this->getValidatoryFactory()
                ->make([], []);
        }
        return $this->validator;
    }

    protected function getCreatingRules(): array
    {
        return $this->creatingRules;
    }

    protected function getUpdatingRules(): array
    {
        return $this->updatingRules;
    }

    protected function validateCallable(string $method): static
    {
        if ($method == self::$METHOD_CREATING && method_exists($this, 'validateWhenCreate')) {
            $this->validateWhenCreate();
        }
        if ($method == self::$METHOD_UPDATING && method_exists($this, 'validateWhenUpdate')) {
            $this->validateWhenUpdate();
        }
        return $this;
    }

    protected function getCreatingValidationAttributes(): array
    {
        return $this->getAttributes();
    }

    protected function getUpdatingValidationAttributes(): array
    {
        return $this->getAttributes();
    }
}
