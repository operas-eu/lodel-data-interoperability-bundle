# Developer Documentation 

This bundle includes developer-oriented documentation generated with _Doxygen_, based on PHPDoc comments in the source code. It helps contributors understand the internal structure and responsibilities of the codebase.

## Prerequisites

Make sure you have _Doxygen_ installed on your system:

```bash
$ sudo apt-get install doxygen
```

## Generate the Documentation

From the root of the project, run:

```bash
$ doxygen Doxyfile
```

The generated documentation will be available in the ```doc/html/``` directory.
Open ```doc/html/index.html``` in your browser to browse the documentation.

## Keep It Up-to-Date

Whenever you make changes to the codebase, rerun the command above to regenerate the documentation and keep it current.
