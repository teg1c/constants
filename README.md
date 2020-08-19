# 注解枚举类

```
composer require tegic/constants
```

## 使用

定义
```php
use tegic\Constants;

class Enum extends Constants
{
     /**
     * @Message("test")
     */
     CONST TEST = 1;
    /**
     * @Message("test1:%s,%s")
     */
     CONST TEST1 = 2;
}

```

使用

```php

Enum::getMessage(Enum::TEST);
Enum::getMessage(Enum::TEST1,['1','2']);

```