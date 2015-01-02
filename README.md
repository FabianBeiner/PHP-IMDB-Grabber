# PHP IMDb.com Grabber

**This PHP library enables you to scrap data from IMDB.com.**

*The script is a proof of concept. It’s mostly working, but you shouldn’t use it. IMDb doesn’t allow this method of data fetching. I personally do not use or promote this script, you’re fully responsive if you’re using it.*

The technique used is called “[web scraping](http://en.wikipedia.org/wiki/Web_scraping "Web scraping at Wikipedia")”. This means, if IMDb changes anything within their HTML source, the script is most likely going to fail. I won’t update this regularly, so don’t count on it to be working all the time.

## License

[The MIT License (MIT)](http://fabianbeiner.mit-license.org/ "The MIT License")

## Usage

See **examples** directory.

## Bugs?

If you run into any malfunctions, feel free to submit an issue. Make sure to enable debugging: `const IMDB_DEBUG = true;` in `imdb.class.php`.