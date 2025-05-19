# CHANGELOG

## [2.x] - 2025-02-01

If you are upgrading from a version `1.x` to `2.x`, please perform the following steps:

### Breaking Changes

### 1. Namespace Change
- **Old Namespace:** `DeepseekPhp`
- **New Namespace:** `DeepSeek`

**Action Required:**
Update all imports in your codebase.

##### Replace:
```php
use DeepseekPhp\Someclass;
```

##### With:
```php
use DeepSeek\Someclass;
```

### Migration Guide
1. Replace all occurrences of `DeepseekPhp` with `DeepSeek` in your code.
3. Run tests to ensure everything works as expected.

If you encounter issues, please refer to our documentation or open an issue on GitHub.

