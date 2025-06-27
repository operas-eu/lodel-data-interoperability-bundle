import os
import sys
sys.path.insert(0, os.path.abspath('..'))

project = 'Lodel Data Interoperability Bundle'
author = 'OpenEdition / CRAFT-OA Project'
release = '0.1.0'

extensions = [
    'sphinx.ext.autodoc',
    'sphinx.ext.napoleon',
    'sphinx.ext.viewcode',
    'myst_parser',
]

source_suffix = {
    '.md': 'markdown',
    '.rst': 'restructuredtext',
}

html_theme = 'furo'
