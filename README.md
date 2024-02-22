# Infinity Game
craft infinite items with help of ollama or any other language model.

for now this project is very small and kind of generic, but I hope to improve over time.

### my todo list:
* ### implementing config for credentials and endpoint of desired language models
    for now this game requires local language model the endpoint is hardcoded since this game is only a view hours old, from scratch to this version.
* ### add some more love to ui and elements
    will add some sounds and animation.
* ### make new discoveries personal
  once this game is online, would be nice if there is a list of users who have discovered new items.
* ### hints
  before users get bored give them a hint how to discover what ever they like to discover.
* ### much much more ...

## Quickstart
To run and play, all you need is a local language model. Head over to [Ollama](https://github.com/ollama/ollama) to learn more
about installing and running a local language model.

smack a copy of this as a brand new project like myInfinityGame on your machine and run 

 ```
 composer install 
 ```

Start your language model set your endpoint in
client class - infiniteGame/Controller/Ollama/Client.php

 ```
public static string $endpoint = 'http://127.0.0.1:11434'; // put your endpoint here

public static string $path = '/api';
public static string $model = 'openchat:latest';  // put your model here
 ```
start your wampp, xampp, whatever you run php with run index.php on your local machine i.e. http://localhost:63342/myInfinityGame/ and have fun.

Would love to see your version / improvements

