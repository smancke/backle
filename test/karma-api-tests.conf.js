module.exports = function (config) {
  config.set({
    basePath: '../',

    files: [
        'app/lib/jquery.min.js',
        'test/api/**/*.js'
    ],

      proxies: {
          '/api/': 'http://127.0.0.1/backle/api/',
          '/c/': 'http://127.0.0.1/backle/c/',
          '/backle/': 'http://127.0.0.1/backle/',
      },
      
    frameworks: ['jasmine'],

    autoWatch: true,

    browsers: ['Chrome'],

    junitReporter: {
      outputFile: 'test_out/unit.xml',
      suite: 'unit'
    }
  });
};
