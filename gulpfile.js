/* Gulp General Packages */
"use strict";
const gulp = require("gulp");
const path = require("path");
const themeDirectory = `web/app/themes/whatjackhasmade`;

/* SCSS/CSS Packages */
const sass = require("gulp-sass");
const sassGlob = require("gulp-sass-glob");
const plumber = require("gulp-plumber");
const notify = require("gulp-notify");
const cleanCSS = require("gulp-clean-css");
const autoprefixer = require("gulp-autoprefixer");

/* Browsersync Packages */
const browserSync = require("browser-sync").create();

/* JS Babel and Minifcation Packages */
const babel = require("gulp-babel");
const concat = require("gulp-concat");
const sourcemaps = require("gulp-sourcemaps");
const uglify = require("gulp-uglify-es").default;

/* Gulp Task: Browsersync change on file updates */
gulp.task("watch", () => {
	browserSync.init({
		proxy: "https://whatjackhasmade.local",
	});

	gulp.watch(`${themeDirectory}/*.php`).on("change", browserSync.reload);
});

/* Gulp Task: run `gulp` in the terminal */
gulp.task("default", gulp.series(["watch"]));
