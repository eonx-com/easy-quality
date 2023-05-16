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

    class MyAnnotationClass
    {
        /**
         * @Assert\AssertFirst()
         * @Assert\AssertSecond()
         *
         * @param string $firstVar
         * @param string $secondVar
         *
         * @return void
         *
         * @AppAssertCustomAssertFirst()
         * @AppAssertCustomAssertSecond()
         */
        public function doSomething(string $firstVar, string $secondVar): void
        {

        }
    }
}
-----
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

    class MyAnnotationClass
    {
        /**
         * @return void
         */
        public function doSomething(string $firstVar, string $secondVar): void
        {

        }
    }
}
