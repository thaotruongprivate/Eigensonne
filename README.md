Hacker News
====

This website displays top news from [Hacker News](https://news.ycombinator.com/news). It basically just displays those news in a different style. It seems to me that [this API endpoint](https://hacker-news.firebaseio.com/v0/topstories.json) returns what's displayed on the site so I used it for this app. 

### Tech stack
- silex
- phpunit for testing
- bootstrap css

### Installation

- clone this repository
- make sure you have composer installed
- go to the project root
- run 
  ```
  composer install
  ```
- to serve the website run
  ```
  cd web
  php -S localhost:8000

  ```
  
### Usage
- to see the news page go to "/news", by default only 10 pieces of news are displayed per page, and the default page is page 1.
- clicking on "Next" will take you to "/news?p=2"
- there can also be other parameters in the url, e.g "/news?p=2&query=fresh", the "Previous" and "Next" links will contain all the parameters used

### Test
```
./vendor/bin/phpunit
```

For all questions related to this app please contact Thao Lipasti at truong.t.n.thao@gmail.com.
  
  
  


