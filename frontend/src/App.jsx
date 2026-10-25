import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import "./App.css";

import Events from "../pages/student/dashboard";

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<Navigate to="/login" />} />
                <Route path="/login" element={<Events />} />

            </Routes>
        </BrowserRouter>
        
    );
}

export default App;