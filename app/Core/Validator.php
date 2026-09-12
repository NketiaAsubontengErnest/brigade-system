<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Input Validator.
 */
class Validator
{
    private array $errors = [];
    private array $data;
    private array $rules;
    private array $messages = [];

    /**
     * Validate data against rules.
     */
    public function validate(array $data, array $rules, array $messages = []): bool
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $this->validateField($field, $fieldRules);
        }

        return empty($this->errors);
    }

    /**
     * Validate a single field.
     */
    private function validateField(string $field, array $rules): void
    {
        $value = $this->data[$field] ?? null;
        $label = $this->messages[$field] ?? $this->humanize($field);

        foreach ($rules as $rule) {
            $params = [];

            if (str_contains($rule, ':')) {
                [$ruleName, $paramString] = explode(':', $rule, 2);
                $params = explode(',', $paramString);
                $rule = $ruleName;
            }

            switch ($rule) {
                case 'required':
                    if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                        $this->errors[$field][] = "{$label} is required.";
                    }
                    break;

                case 'email':
                    if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = "{$label} must be a valid email address.";
                    }
                    break;

                case 'phone':
                    if ($value && !preg_match('/^[\+]?[0-9\s\-\(\)]{7,15}$/', $value)) {
                        $this->errors[$field][] = "{$label} must be a valid phone number.";
                    }
                    break;

                case 'min':
                    if ($value && strlen($value) < (int)$params[0]) {
                        $this->errors[$field][] = "{$label} must be at least {$params[0]} characters.";
                    }
                    break;

                case 'max':
                    if ($value && strlen($value) > (int)$params[0]) {
                        $this->errors[$field][] = "{$label} must not exceed {$params[0]} characters.";
                    }
                    break;

                case 'numeric':
                    if ($value && !is_numeric($value)) {
                        $this->errors[$field][] = "{$label} must be a number.";
                    }
                    break;

                case 'integer':
                    if ($value && !ctype_digit((string)$value)) {
                        $this->errors[$field][] = "{$label} must be a whole number.";
                    }
                    break;

                case 'date':
                    if ($value) {
                        $d = \DateTime::createFromFormat('Y-m-d', $value);
                        if (!$d || $d->format('Y-m-d') !== $value) {
                            $this->errors[$field][] = "{$label} must be a valid date (YYYY-MM-DD).";
                        }
                    }
                    break;

                case 'datetime':
                    if ($value) {
                        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                        if (!$d) {
                            $this->errors[$field][] = "{$label} must be a valid date and time.";
                        }
                    }
                    break;

                case 'in':
                    if ($value && !in_array($value, $params)) {
                        $this->errors[$field][] = "{$label} must be one of: " . implode(', ', $params) . ".";
                    }
                    break;

                case 'confirmed':
                    $confirmField = $field . '_confirmation';
                    if (($this->data[$confirmField] ?? null) !== $value) {
                        $this->errors[$field][] = "{$label} confirmation does not match.";
                    }
                    break;

                case 'unique':
                    // unique:table,column
                    if ($value && count($params) >= 2) {
                        $table = $params[0];
                        $column = $params[1];
                        $exceptId = $params[2] ?? null;
                        $db = Database::getInstance();
                        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :value";
                        $bindings = ['value' => $value];
                        if ($exceptId) {
                            $sql .= " AND id != :id";
                            $bindings['id'] = $exceptId;
                        }
                        $count = $db->fetchOne($sql, $bindings);
                        if ((int)$count['count'] > 0) {
                            $this->errors[$field][] = "{$label} is already taken.";
                        }
                    }
                    break;

                case 'file':
                    $file = $_FILES[$field] ?? null;
                    if ($file && $file['error'] !== UPLOAD_ERR_OK) {
                        $this->errors[$field][] = "{$label} file upload failed.";
                    }
                    break;

                case 'image':
                    $file = $_FILES[$field] ?? null;
                    if ($file && $file['error'] === UPLOAD_ERR_OK) {
                        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $file['tmp_name']);
                        finfo_close($finfo);
                        if (!in_array($mimeType, $allowed)) {
                            $this->errors[$field][] = "{$label} must be an image file (JPEG, PNG, GIF, or WebP).";
                        }
                    }
                    break;

                case 'max_file_size':
                    $file = $_FILES[$field] ?? null;
                    if ($file && $file['error'] === UPLOAD_ERR_OK) {
                        $maxSize = (int)($params[0] ?? 5242880); // Default 5MB
                        if ($file['size'] > $maxSize) {
                            $this->errors[$field][] = "{$label} file size must not exceed " . round($maxSize / 1048576, 1) . "MB.";
                        }
                    }
                    break;

                case 'alpha':
                    if ($value && !preg_match('/^[a-zA-Z\s]+$/', $value)) {
                        $this->errors[$field][] = "{$label} must contain only letters.";
                    }
                    break;

                case 'alpha_num':
                    if ($value && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                        $this->errors[$field][] = "{$label} must contain only letters and numbers.";
                    }
                    break;

                case 'url':
                    if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                        $this->errors[$field][] = "{$label} must be a valid URL.";
                    }
                    break;

                case 'boolean':
                    if ($value !== null && $value !== '' && !in_array((int)$value, [0, 1], true)) {
                        $this->errors[$field][] = "{$label} must be true or false.";
                    }
                    break;

                case 'same':
                    // same:field_to_match
                    if ($value !== ($this->data[$params[0]] ?? null)) {
                        $this->errors[$field][] = "{$label} must match {$params[0]}.";
                    }
                    break;

                case 'nullable':
                    // Already handled by not requiring the field
                    break;

                case 'currency':
                    if ($value !== null && $value !== '') {
                        if (!preg_match('/^\d+(\.\d{1,2})?$/', (string)$value) || (float)$value < 0) {
                            $this->errors[$field][] = "{$label} must be a valid currency amount.";
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Get validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error for a field.
     */
    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Check if there are errors.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get old input value.
     */
    public function old(string $key, mixed $default = null): mixed
    {
        $session = new Session();
        $oldData = $session->get('_old_input', []);
        return $oldData[$key] ?? $default;
    }

    /**
     * Store input data for next request.
     */
    public static function flashInput(array $data): void
    {
        $session = new Session();
        $session->set('_old_input', $data);
    }

    /**
     * Convert field name to human-readable label.
     */
    private function humanize(string $field): string
    {
        $label = str_replace(['_', '-'], ' ', $field);
        return ucwords($label);
    }

    /**
     * Get error message HTML for a field.
     */
    public static function errorHtml(string $field, array $errors): string
    {
        if (!isset($errors[$field])) {
            return '';
        }
        $error = $errors[$field][0];
        return '<div class="invalid-feedback d-block">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
    }
}
