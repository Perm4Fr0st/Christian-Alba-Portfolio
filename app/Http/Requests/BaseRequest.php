<?php

namespace App\Requests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidatesWhenResolvedTrait;
use Laravel\Lumen\Http\Redirector;

/**
 * Class FormRequest.
 */
class BaseRequest extends Request implements ValidatesWhenResolved
{
    use ValidatesWhenResolvedTrait;

    /**
     * The container instance.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * The redirector instance.
     *
     * @var Redirector
     */
    protected Redirector $redirector;

    /**
     * The URI to redirect to if validation fails.
     *
     * @var string
     */
    protected string $redirect;

    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected string $redirectRoute;

    /**
     * The controller action to redirect to if validation fails.
     *
     * @var string
     */
    protected string $redirectAction;

    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected string $errorBag = 'default';

    /**
     * Get the validator instance for the request.
     *
     * @return Validator
     * @throws BindingResolutionException
     */
    protected function getValidatorInstance(): Validator
    {
        $factory = $this->container->make(ValidationFactory::class);

        if (method_exists($this, 'validator')) {
            $validator = $this->container->call([$this, 'validator'], compact('factory'));
        } else {
            $validator = $this->createDefaultValidator($factory);
        }

        if (method_exists($this, 'withValidator')) {
            $this->withValidator($validator);
        }

        return $validator;
    }

    /**
     * Create the default validator instance.
     *
     * @param ValidationFactory $factory
     * @return Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory): Validator
    {
        return $factory->make(
            $this->validationData(),
            $this->container->call([$this, 'rules']),
            $this->messages(),
            $this->attributes()
        );
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData(): array
    {
        return $this->all();
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param array $errors
     * @return JsonResponse
     */
    public function response(array $errors): JsonResponse
    {
        return new JsonResponse($errors, 422);
    }

    /**
     * Get the response for a forbidden operation.
     *
     * @return JsonResponse
     */
    public function forbiddenResponse(): JsonResponse
    {
        return new JsonResponse('Forbidden', 403);
    }

    /**
     * Format the errors from the given Validator instance.
     *
     * @param Validator $validator
     * @return array
     */
    protected function formatErrors(Validator $validator): array
    {
        return $validator->getMessageBag()->toArray();
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated(): array
    {
        $rules = $this->container->call([$this, 'rules']);

        return $this->only(collect($rules)->keys()->map(function ($rule) {
            return Str::contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Set the Redirector instance.
     *
     * @param Redirector $redirector
     * @return $this
     */
    public function setRedirector(Redirector $redirector): self
    {
        $this->redirector = $redirector;

        return $this;
    }

    /**
     * Set the container implementation.
     *
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }
}