var del = require('del');
var gulp = require('gulp');
var include = require('gulp-include');
var runSequence = require('gulp-run-sequence');

var TASK_BUILD = 'build';
var TASK_DIST = 'dist';

var target = '_previewall.php';

gulp.task(TASK_BUILD, function(cb) {
	return gulp.src('src' + '/' + target).
		pipe(include()).
		pipe(gulp.dest(TASK_BUILD)).
		on('end', function() { cb; })
	;
});

gulp.task(TASK_DIST, function(cb) {
	return gulp.src(TASK_BUILD + '/' + target).
		pipe(gulp.dest(TASK_DIST))
	;
});

gulp.task('clean', function(cb) {
	del(['./' + TASK_DIST, './' + TASK_BUILD], cb);
});

gulp.task('default', function() {
	runSequence(TASK_BUILD, TASK_DIST);
});
