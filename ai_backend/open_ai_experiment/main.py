from flask import Flask, request, jsonify
from openai import OpenAI
import os

# Setup Flask
app = Flask(__name__)

# Configure OpenAI
client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))  # Replace with your key

@app.route('/chat', methods=['POST'])
def chat():
    data = request.get_json()
    prompt = data.get('prompt', '')

    if not prompt:
        return jsonify({'response': '⚠️ No prompt provided.'}), 400

    try:
        completion = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "You are an AI assistant helping hostel administrators."},
                {"role": "user", "content": prompt}
            ]
        )
        ai_reply = completion.choices[0].message.content.strip()
        return jsonify({'response': ai_reply})
    except Exception as e:
        print(e)
        return jsonify({'response': '⚠️ Error generating response: ' + str(e)})

if __name__ == '__main__':
    app.run(port=5000)
