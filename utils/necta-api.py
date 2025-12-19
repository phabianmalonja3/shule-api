from nectaapi import schools,summary
import json


data = summary.summary(2018,exam_type='csee',school_number='s1835')
print(json.dumps(data))
