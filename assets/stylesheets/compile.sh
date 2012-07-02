#!/bin/bash
lessc -x base.less >main.css
lessc -x layout.less >>main.css
lessc -x colors.less >>main.css