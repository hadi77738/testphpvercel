{
  "version": 2,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.4"
    }
  },
  "rewrites": [
    {
      "source": "/(.*)",
      "destination": "/api/index.php"
    }
  ]
}