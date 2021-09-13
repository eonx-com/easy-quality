<?php

namespace Some\Module\Constraint {
    class CustomAssertFirst {

    }

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
    use Some\Module\Constraint\CustomAssertSecond as AppAssertCustomAssertSecond;
    use Assert;

    class MyAnnotationClass
    {
        /**
         * @Assert\AssertFirst()
         * @Assert\AssertSecond()
         *
         * @AppAssertCustomAssertFirst()
         * @AppAssertCustomAssertSecond()
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