
# PHP Twitter Bot

Twitter bot that tweets with text, creates images and tweets and loads of more features


## Authors

- [@Mackral Gonsalves](https://github.com/Mackral02)


## 🚀 About Me
I'm a UI/Frontend developer...

[![Mackral's GitHub stats](https://github-readme-stats.vercel.app/api?username=mackral02&count_private=true&show_icons=true&theme=dark)](https://github.com/mackral02/)
[![Top Langs](https://github-readme-stats.vercel.app/api/top-langs/?username=mackral02&layout=compact)](https://github.com/mackral02)
# Mackral's merge text and image
## Demo code for fetching the image.
```sh
var formdata = new FormData();
formdata.append("quote", "");
formdata.append("author", "");
formdata.append("category", "");

var requestOptions = {
  method: 'POST',
  body: formdata,
  redirect: 'follow'
};

fetch("image-merge-api.php", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
```

## License

MIT

**Free Software, Hell Yeah!**
