<?php

namespace Some\Module\Constraint {
    class CustomAssertFirst {

    }
}

namespace Some\Other\Module\Constraint {
    class CustomAssertSecond {

    }
}

namespace Assert {
    class AssertFirst {

    }

    class AssertSecond {

    }
}

namespace Test {
    use Some\Module\Constraint\CustomAssertFirst as AppAssertCustomAssertFirst;
    use Some\Other\Module\Constraint\CustomAssertSecond as AppAssertCustomAssertSecond;
    use Assert;

    class MyAnnotationClass
    {
        /**
         * @AppAssertCustomAssertFirst()
         * @AppAssertCustomAssertSecond()
         *
         * @Assert\AssertFirst()
         * @Assert\AssertSecond()
         *
         * @param string $firstVar
         * @param string $secondVar
         *
         * @return void
         */
        public function doSomething(string $firstVar, string $secondVar): void
        {

        }
    }
}

