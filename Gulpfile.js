var gulp=require('gulp');
var sass=require('gulp-ruby-sass');
var autoprefixer=require('gulp-autoprefixer');

gulp.task('css', function(){
   gulp.src('app/assets/sass/main.sass')
       .pipe(sass({style: 'compressed'}))
       .pipe(autoprefixer('last 10 version'))
       .pipe(gulp.dest('public/css'));
});

gulp.task('watch', function(){
   gulp.watch('app/assets/sass/**/*.sass', ['css']);
});

gulp.task('default',['watch']);

