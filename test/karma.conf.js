module.exports = function (config) {
  config.set({
    basePath: '../',

    files: [
        'lib/jquery.js',
        'lib/ui/jquery-ui.js',
        'lib/angular.min.js',
        'lib/angular-*.min.js',
        'test/lib/angular-mocks.js',
        '*.js',
        'test/unit/**/*.js'
    ],

    frameworks: ['jasmine'],

    autoWatch: true,

    browsers: ['Chrome'],

    junitReporter: {
      outputFile: 'test_out/unit.xml',
      suite: 'unit'
    }
  });
};
