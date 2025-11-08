from flask import Flask, request, jsonify
import subprocess, mysql.connector, re

app = Flask(__name__)

# --- MySQL connection details ---
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "root",
    "database": "hostel_management",
    "port": 8889
}

def query_db(sql, params=None):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute(sql, params or ())
        result = cursor.fetchall()
        cursor.close()
        conn.close()
        return result
    except Exception as e:
        return f"DB Error: {e}"

# --- Local LLM call (Ollama) ---
def call_llm(prompt):
    result = subprocess.run(
        ["ollama", "run", "phi3", prompt],
        capture_output=True, text=True, timeout=40
    )
    return result.stdout.strip() or "⚠️ No response from local model."

@app.route("/chat", methods=["POST"])
def chat():
    data = request.get_json()
    prompt = data.get("prompt", "").lower().strip()
    if not prompt:
        return jsonify({"response": "⚠️ No prompt provided."}), 400

    # --- simple keyword router ---
    try:
        if "complaint" in prompt:
            rows = query_db("SELECT complaint_type, status, created_at FROM complaints ORDER BY created_at DESC LIMIT 10;")
            if isinstance(rows, str):  # DB error
                return jsonify({"response": rows})
            if not rows:
                return jsonify({"response": "No complaints found."})
            formatted = "\n".join([f"• {r[0]} — {r[1]} ({r[2]})" for r in rows])
            return jsonify({"response": "Here are recent complaints:\n" + formatted})

        elif "fee" in prompt or "unpaid" in prompt:
            rows = query_db("SELECT semester, amount, status FROM fees ORDER BY semester;")
            if isinstance(rows, str):
                return jsonify({"response": rows})
            paid = sum(float(r[1]) for r in rows if r[2].lower()=="paid")
            unpaid = sum(float(r[1]) for r in rows if r[2].lower()=="unpaid")
            return jsonify({"response": f"Total paid ₹{paid}, unpaid ₹{unpaid}. Records: {len(rows)}"})

        elif "maintenance" in prompt:
            rows = query_db("SELECT request_type, status, created_at FROM maintenance ORDER BY created_at DESC LIMIT 10;")
            if isinstance(rows, str):
                return jsonify({"response": rows})
            if not rows:
                return jsonify({"response": "No maintenance requests yet."})
            formatted = "\n".join([f"• {r[0]} — {r[1]} ({r[2]})" for r in rows])
            return jsonify({"response": "Recent maintenance requests:\n" + formatted})
        
        elif "guest" in prompt:
            rows = query_db("SELECT guest_name, relationship, visit_date, status FROM guest_log ORDER BY created_at DESC LIMIT 10;")
            if isinstance(rows, str):  # DB error string
                return jsonify({"response": rows})
            if not rows:
                return jsonify({"response": "No guest requests found."})
            formatted = "\n".join([f"• {r[0]} ({r[1]}) — {r[3]} visit on {r[2]}" for r in rows])
            return jsonify({"response": "Here are recent guest requests:\n" + formatted})
        
        elif "notice" in prompt:
            rows = query_db("SELECT title, start_date, end_date FROM notices ORDER BY created_at DESC LIMIT 5;")
            if isinstance(rows, str):
                return jsonify({"response": rows})
            if not rows:
                return jsonify({"response": "No notices available right now."})
            formatted = "\n".join([f"• {r[0]} (Active: {r[1]} to {r[2]})" for r in rows])
            return jsonify({"response": "Here are the latest notices:\n" + formatted})

        elif "mess" in prompt or "feedback" in prompt:
            rows = query_db("SELECT rating_quality, rating_hygiene, rating_service, message, created_at FROM mess_feedback ORDER BY created_at DESC LIMIT 5;")
            if isinstance(rows, str):
                return jsonify({"response": rows})
            if not rows:
                return jsonify({"response": "No mess feedback submitted yet."})
            formatted = "\n".join([
                f"• Quality: {r[0]}, Hygiene: {r[1]}, Service: {r[2]} — \"{r[3]}\" ({r[4]})"
                for r in rows
            ])
            return jsonify({"response": "Recent mess feedback entries:\n" + formatted})

        elif "student" in prompt or "profile" in prompt:
            rows = query_db("SELECT full_name, room_number, block, room_type FROM students ORDER BY student_id DESC LIMIT 5;")
            if isinstance(rows, str):
                return jsonify({"response": rows})
            if not rows:
                return jsonify({"response": "No student records found."})
            formatted = "\n".join([
                f"• {r[0]} — Room {r[1]} ({r[2]} Block, {r[3]})" for r in rows
            ])
            return jsonify({"response": "Here are recent student profiles:\n" + formatted})

        else:
            # fall-back to local LLM for general talk
            reply = call_llm(prompt)
            return jsonify({"response": reply})

    except Exception as e:
        return jsonify({"response": f"❌ Error: {e}"})

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000)
