import { useEffect, useState } from "react";
import api from "./api";

export default function Events() {
    const [message, setMessage] = useState("Connecting...");
    const [error, setError] = useState("");

    useEffect(() => {
        api.get("/events")
            .then((response) => {
                setMessage(response.data.message);
            })
            .catch((err) => {
                console.error(err);
                setError("Hindi makonekta sa LavaLust API.");
            });
    }, []);

    return (
        <div>
            <h1>CampusConnect Events</h1>
            <p>{message}</p>
            {error && <p>{error}</p>}
        </div>
    );
}