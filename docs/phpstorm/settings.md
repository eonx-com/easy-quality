---eonx_docs--- title: PhpStorm settings weight: 3000 is_section: true ---eonx_docs---

### PhpStorm code style and arrangement

The ECS set ([config/ecs/eonx-set.php](../../config/ecs/eonx-set.php)) enforces the order of class members via
`PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer` (alphabetical sorting within each visibility/static/abstract
group, case-sensitive). If PhpStorm's "Rearrange Code" is configured differently, the IDE and ECS will fight each other
and you will have to run `fix-ecs` after every rearrange.

This package ships a PhpStorm code style scheme that produces the same order as the fixer:

- [config/phpstorm/Project.xml](../../config/phpstorm/Project.xml) — the code style scheme (including arrangement rules)
- [config/phpstorm/codeStyleConfig.xml](../../config/phpstorm/codeStyleConfig.xml) — enables the per-project scheme

### Installation

1. Copy both files into the `.idea/codeStyles/` directory of your project (create the directory if it does not exist):

    ```shell
    mkdir -p .idea/codeStyles
    cp quality/vendor/eonx-com/easy-quality/config/phpstorm/Project.xml .idea/codeStyles/
    cp quality/vendor/eonx-com/easy-quality/config/phpstorm/codeStyleConfig.xml .idea/codeStyles/
    ```

2. Restart PhpStorm (or `File | Invalidate Caches` is not needed — reopening the project is enough for the scheme to be
   picked up).
3. Commit `.idea/codeStyles/` to Git so the whole team shares the same settings.

If your project already has its own `Project.xml` with custom settings for other languages, copy only the
`codeStyleSettings language="PHP"` block (or at least the `<arrangement>` section inside it).

### Usage

- `Code | Reformat Code` (⌥⌘L / Ctrl+Alt+L) with the **Rearrange entries** checkbox enabled (press ⌥⇧⌘L / Ctrl+Alt+Shift+L
  to open the dialog with the checkbox).
- Optionally enable `Settings | Tools | Actions on Save | Reformat code` with **Rearrange code** to keep files sorted
  automatically.

### What the arrangement matches

The rules mirror the `OrderedClassElementsFixer` configuration of the ECS set:

1. Constants: `public` → `protected` → `private`, each group sorted by name
2. Properties: `public static` → `public` → `protected static` → `protected` → `private static` → `private`, by name
3. `__construct`, `__destruct`, then other magic methods (`__*`) by name
4. PHPUnit lifecycle methods in the fixer's canonical order: `setUpBeforeClass`, `doSetUpBeforeClass`,
   `tearDownAfterClass`, `doTearDownAfterClass`, `setUp`, `doSetUp`, `assertPreConditions`, `assertPostConditions`,
   `tearDown`, `doTearDown`
5. Methods: `public abstract static` → `public static` → `public abstract` → `public` → same for `protected` →
   same for `private`, each group sorted by name

Sorting is case-sensitive on both sides: PhpStorm's `BY_NAME` uses Java's `String.compareTo()` and the fixer is
configured with `case_sensitive: true`, so uppercase letters sort before lowercase ones (e.g. `addZone()` comes before
`address()`).

### Known behavior notes

- **`final` does not affect sorting.** `OrderedClassElementsFixer` only distinguishes visibility, `static` and
  `abstract`; a `final public` method is sorted among regular public methods by name. The shipped arrangement rules
  deliberately contain no `FINAL` conditions. Do not add `final`-based rules in PhpStorm — the fixer cannot replicate
  them.
- **Trait `use` statements and enum cases** are not covered by PhpStorm arrangement rules. The fixer sorts them
  alphabetically (`use_trait` and `case` groups); PhpStorm leaves them untouched, so in the rare case they are
  unsorted, only `fix-ecs` will reorder them.
- **`.editorconfig` cannot replace this config.** PhpStorm's EditorConfig support (`ij_php_*` properties) covers
  formatting options only — arrangement rules cannot be expressed in `.editorconfig` at all. Keep a minimal,
  editor-agnostic `.editorconfig` (charset, indentation, line endings, max line length) and ship the arrangement via
  `.idea/codeStyles/`.

### Verifying the setup

Paste a class with shuffled members (constants of mixed visibility, `setUpBeforeClass`, `setUp`, magic methods, a
`final` method), run `Code | Reformat Code` with **Rearrange entries**, then run ECS on the same file — ECS should
report nothing to fix.
