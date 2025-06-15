  // JavaScript to toggle profile dropdown
document.addEventListener("DOMContentLoaded", function () {
  const profileLink = document.querySelector('.profile-dropdown a');
  if (profileLink) {
    profileLink.addEventListener('click', function(e) {
      e.preventDefault();
      const dropdown = document.querySelector('.dropdown-content');
      dropdown.classList.toggle('show');
    });

    document.addEventListener('click', function(e) {
      const dropdown = document.querySelector('.dropdown-content');
      if (dropdown && !e.target.closest('.profile-dropdown')) {
        dropdown.classList.remove('show');
      }
    });
  }
});

// Chatbot responses
// Toggle chatbot visibility
document.getElementById("chatbot-toggle").addEventListener("click", () => {
  const chatbotContainer = document.getElementById("chatbot-container");
  if (chatbotContainer.style.display === "none" || chatbotContainer.style.display === "") {
      chatbotContainer.style.display = "block"; // Show chatbot
  } else {
      chatbotContainer.style.display = "none"; // Hide chatbot
  }
});

// Close chatbot when the close button is clicked
document.getElementById("close-chatbot").addEventListener("click", () => {
  document.getElementById("chatbot-container").style.display = "none";
});

// Function to fetch chatbot response from the backend
async function getChatbotResponse(userMessage) {
  try {
      const response = await fetch("chatbot.php", {
          method: "POST",
          headers: { 
              "Content-Type": "application/json"
          },
          body: JSON.stringify({ message: userMessage })
      });

      if (!response.ok) {
          throw new Error(`API request failed with status ${response.status}`);
      }

      // Check if the response is JSON
      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
          // If not JSON, read as text to debug
          const text = await response.text();
          console.error("Non-JSON response:", text);
          throw new Error("Server returned a non-JSON response");
      }

      const data = await response.json();

      // Handle errors in the JSON response
      if (data.error) {
          throw new Error(data.error);
      }

      return data.response || "Sorry, I didn't understand that.";
  } catch (error) {
      console.error("Error fetching chatbot response:", error);
      return "Sorry, something went wrong. Please try again.";
  }
}

// Function to add a message to the chat interface
function addMessageToChat(message, sender) {
  const chatMessages = document.getElementById("chatbot-messages");
  const messageDiv = document.createElement("div");
  messageDiv.classList.add("message", `${sender}-message`);
  messageDiv.textContent = message;
  chatMessages.appendChild(messageDiv);

  // Scroll to the bottom of the chat
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Handle user input and display response
document.getElementById("send-button").addEventListener("click", async () => {
  const userInput = document.getElementById("chatbot-input").value.trim();
  if (!userInput) return;

  // Add user message to chat
  addMessageToChat(userInput, "user");

  // Clear input
  document.getElementById("chatbot-input").value = "";

  // Fetch bot response
  const botReply = await getChatbotResponse(userInput);
  addMessageToChat(botReply, "bot");
});

// Allow pressing Enter to send message
document.getElementById("chatbot-input").addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
      document.getElementById("send-button").click();
  }
});