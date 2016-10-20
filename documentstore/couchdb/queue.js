{
   "_id": "_design/queue",
   "_rev": "11-6958dbee14fb31d6a3dac6d9f468a946",
   "language": "javascript",
   "views": {
       "todo": {
           "map": "function(doc) {\n  if (doc['message-timestamp']) {\n    if (doc.message) {\n    } else {\n       var attempts = 0;\n       \n       if (!(doc['message-attempts'] === undefined)) {\n        attempts = doc['message-attempts'];\n       }\n      \n      if (attempts < 2) \n      {\n        emit(doc['message-timestamp'], doc._id);\n      }\n    }\n  }\n}"
       },
       "failed_to_resolve": {
           "map": "function(doc) {\n  if (doc['message-timestamp']) {\n    if (!doc.message) {\n      var attempts = 0;\n      if (doc['message-attempts']) {\n        attempts = doc['message-attempts'];\n      }\n      if (attempts >= 2) {\n        emit(doc['message-timestamp'], doc._id);\n      }\n    }\n  }\n}"
       }
   }
}